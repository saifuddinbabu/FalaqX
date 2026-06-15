<?php

/**
 * FalaqX Helper - ImageProcessor
 * Resize, crop, thumbnail, watermark, and convert images using GD.
 */
class ImageProcessor
{
    private \GdImage|false $image = false;
    private int    $width  = 0;
    private int    $height = 0;
    private string $type   = '';
    private string $source = '';

    // ── Load ──────────────────────────────────────────────────────────────────

    public function load(string $filePath): self
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Image file not found: {$filePath}");
        }

        $info = getimagesize($filePath);
        if ($info === false) {
            throw new RuntimeException("Not a valid image: {$filePath}");
        }

        [$this->width, $this->height, $typeConst] = $info;
        $this->source = $filePath;

        $this->image = match ($typeConst) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($filePath),
            IMAGETYPE_PNG  => imagecreatefrompng($filePath),
            IMAGETYPE_GIF  => imagecreatefromgif($filePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($filePath),
            default        => throw new RuntimeException("Unsupported image type."),
        };

        $this->type = image_type_to_extension($typeConst, false);
        return $this;
    }

    // ── Transformations ───────────────────────────────────────────────────────

    /** Resize to exact dimensions (may distort). */
    public function resize(int $newWidth, int $newHeight): self
    {
        $this->requireImage();
        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        $this->preserveTransparency($canvas);
        imagecopyresampled($canvas, $this->image, 0, 0, 0, 0,
            $newWidth, $newHeight, $this->width, $this->height);
        $this->image  = $canvas;
        $this->width  = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    /** Scale proportionally so the longest side equals $maxDim. */
    public function scale(int $maxDim): self
    {
        $this->requireImage();
        if ($this->width >= $this->height) {
            $newW = $maxDim;
            $newH = (int) round(($this->height / $this->width) * $maxDim);
        } else {
            $newH = $maxDim;
            $newW = (int) round(($this->width / $this->height) * $maxDim);
        }
        return $this->resize($newW, $newH);
    }

    /** Crop to a region starting at ($x, $y). */
    public function crop(int $newWidth, int $newHeight, int $x = 0, int $y = 0): self
    {
        $this->requireImage();
        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        $this->preserveTransparency($canvas);
        imagecopy($canvas, $this->image, 0, 0, $x, $y, $newWidth, $newHeight);
        $this->image  = $canvas;
        $this->width  = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    /** Smart thumbnail: resize + centre-crop to fit exactly. */
    public function thumbnail(int $thumbW = THUMB_WIDTH, int $thumbH = THUMB_HEIGHT): self
    {
        $this->requireImage();
        $srcRatio  = $this->width  / $this->height;
        $destRatio = $thumbW / $thumbH;

        if ($srcRatio > $destRatio) {
            // Wider than target — fit height, then crop width
            $tempH = $thumbH;
            $tempW = (int) round($this->width * ($thumbH / $this->height));
        } else {
            $tempW = $thumbW;
            $tempH = (int) round($this->height * ($thumbW / $this->width));
        }

        $this->resize($tempW, $tempH);

        $x = (int) (($tempW - $thumbW) / 2);
        $y = (int) (($tempH - $thumbH) / 2);
        return $this->crop($thumbW, $thumbH, $x, $y);
    }

    /** Rotate the image by $degrees (counter-clockwise). */
    public function rotate(float $degrees, int $bgColor = 0): self
    {
        $this->requireImage();
        $this->image  = imagerotate($this->image, $degrees, $bgColor);
        $this->width  = imagesx($this->image);
        $this->height = imagesy($this->image);
        return $this;
    }

    /** Flip: 'horizontal', 'vertical', or 'both'. */
    public function flip(string $direction = 'horizontal'): self
    {
        $this->requireImage();
        $mode = match ($direction) {
            'horizontal' => IMG_FLIP_HORIZONTAL,
            'vertical'   => IMG_FLIP_VERTICAL,
            default      => IMG_FLIP_BOTH,
        };
        imageflip($this->image, $mode);
        return $this;
    }

    /** Apply a greyscale filter. */
    public function greyscale(): self
    {
        $this->requireImage();
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        return $this;
    }

    /**
     * Stamp a text watermark on the image.
     *
     * @param string $text    Watermark text
     * @param string $position 'center', 'bottom-right', 'bottom-left', 'top-right', 'top-left'
     * @param int    $alpha   Transparency 0 (opaque) – 127 (invisible)
     */
    public function watermarkText(string $text, string $position = 'bottom-right', int $alpha = 80): self
    {
        $this->requireImage();
        $color = imagecolorallocatealpha($this->image, 255, 255, 255, $alpha);
        $fontSize = 5; // built-in GD font size 1-5

        $textW = imagefontwidth($fontSize)  * strlen($text);
        $textH = imagefontheight($fontSize);
        $pad   = 10;

        [$x, $y] = match ($position) {
            'center'       => [($this->width - $textW) / 2, ($this->height - $textH) / 2],
            'bottom-left'  => [$pad, $this->height - $textH - $pad],
            'top-right'    => [$this->width - $textW - $pad, $pad],
            'top-left'     => [$pad, $pad],
            default        => [$this->width - $textW - $pad, $this->height - $textH - $pad],
        };

        imagestring($this->image, $fontSize, (int) $x, (int) $y, $text, $color);
        return $this;
    }

    // ── Save / Output ─────────────────────────────────────────────────────────

    /**
     * Save to disk.
     *
     * @param string $outputPath  Full path including filename
     * @param string $format      'jpg','png','gif','webp' (default: same as source)
     * @param int    $quality     JPEG/WebP quality 0-100
     */
    public function save(string $outputPath, string $format = '', int $quality = IMAGE_QUALITY): self
    {
        $this->requireImage();
        $format = $format ?: $this->type;
        $dir    = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        match ($format) {
            'jpg','jpeg' => imagejpeg($this->image, $outputPath, $quality),
            'png'        => imagepng($this->image, $outputPath, (int) round((100 - $quality) / 10)),
            'gif'        => imagegif($this->image, $outputPath),
            'webp'       => imagewebp($this->image, $outputPath, $quality),
            default      => throw new RuntimeException("Unsupported output format: {$format}"),
        };

        return $this;
    }

    /** Output directly to the browser. */
    public function output(string $format = '', int $quality = IMAGE_QUALITY): void
    {
        $this->requireImage();
        $format = $format ?: $this->type;
        header('Content-Type: image/' . ($format === 'jpg' ? 'jpeg' : $format));

        match ($format) {
            'jpg','jpeg' => imagejpeg($this->image, null, $quality),
            'png'        => imagepng($this->image, null, (int) round((100 - $quality) / 10)),
            'gif'        => imagegif($this->image),
            'webp'       => imagewebp($this->image, null, $quality),
            default      => throw new RuntimeException("Unsupported format: {$format}"),
        };
    }

    public function getWidth(): int  { return $this->width;  }
    public function getHeight(): int { return $this->height; }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function requireImage(): void
    {
        if (!$this->image) {
            throw new RuntimeException('No image loaded. Call load() first.');
        }
    }

    private function preserveTransparency(\GdImage $canvas): void
    {
        if (in_array($this->type, ['png', 'gif'], true)) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefill($canvas, 0, 0, $transparent);
        }
    }

    public function __destruct()
    {
        if ($this->image) {
            imagedestroy($this->image);
        }
    }
}
