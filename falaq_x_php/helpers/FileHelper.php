<?php

namespace FalaqX\Helpers;

/**
 * FalaqX Helper - FileHelper
 * Copy, move, delete, upload, list, and manage local files and directories.
 */
class FileHelper
{
    // ── Copy / Move / Delete ──────────────────────────────────────────────────

    /** Copy a single file. Creates the destination directory if needed. */
    public static function copy(string $src, string $dest): bool
    {
        self::ensureDir(dirname($dest));
        return copy($src, $dest);
    }

    /** Copy a directory recursively. */
    public static function copyDir(string $src, string $dest): void
    {
        if (!is_dir($src)) {
            throw new \RuntimeException("Source directory not found: {$src}");
        }
        self::ensureDir($dest);

        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        ) as $item) {
            $target = $dest . DIRECTORY_SEPARATOR . str_replace($src . DIRECTORY_SEPARATOR, '', $item->getPathname());
            if ($item->isDir()) {
                self::ensureDir($target);
            } else {
                copy($item->getPathname(), $target);
            }
        }
    }

    /** Move (rename) a file or directory. */
    public static function move(string $src, string $dest): bool
    {
        self::ensureDir(dirname($dest));
        return rename($src, $dest);
    }

    /** Delete a single file. */
    public static function delete(string $path): bool
    {
        if (is_file($path)) {
            return unlink($path);
        }
        return false;
    }

    /** Delete a directory and all its contents. */
    public static function deleteDir(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        ) as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        return rmdir($path);
    }

    // ── Upload ────────────────────────────────────────────────────────────────

    /**
     * Handle a file upload from $_FILES.
     *
     * @param string $fieldName     $_FILES key
     * @param string $uploadDir     Destination directory (default: UPLOAD_PATH)
     * @param bool   $sanitizeName  Replace special chars in filename
     * @return array  ['success', 'path', 'name', 'size', 'error']
     */
    public static function upload(
        string $fieldName,
        string $uploadDir  = UPLOAD_PATH,
        bool   $sanitizeName = true
    ): array {
        if (!isset($_FILES[$fieldName])) {
            return ['success' => false, 'error' => "No file field [{$fieldName}] found."];
        }

        $file = $_FILES[$fieldName];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => self::uploadErrorMessage($file['error'])];
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $maxMb = UPLOAD_MAX_SIZE / 1048576;
            return ['success' => false, 'error' => "File exceeds the {$maxMb} MB limit."];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
            return ['success' => false, 'error' => "Extension [{$ext}] is not allowed."];
        }

        $baseName = $sanitizeName ? self::sanitizeFilename($file['name']) : $file['name'];
        $baseName = pathinfo($baseName, PATHINFO_FILENAME) . '_' . uniqid() . '.' . $ext;

        self::ensureDir($uploadDir);
        $dest = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $baseName;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'error' => 'Could not move uploaded file.'];
        }

        return [
            'success' => true,
            'path'    => $dest,
            'name'    => $baseName,
            'size'    => $file['size'],
            'error'   => null,
        ];
    }

    // ── Read / Write ──────────────────────────────────────────────────────────

    public static function read(string $path): string
    {
        if (!is_file($path)) {
            throw new \RuntimeException("File not found: {$path}");
        }
        return file_get_contents($path);
    }

    public static function write(string $path, string $content, bool $append = false): int|false
    {
        self::ensureDir(dirname($path));
        return file_put_contents($path, $content, $append ? FILE_APPEND | LOCK_EX : LOCK_EX);
    }

    // ── Directory listing ─────────────────────────────────────────────────────

    /** List files in a directory matching an optional extension filter. */
    public static function listFiles(string $dir, string $extension = ''): array
    {
        if (!is_dir($dir)) {
            return [];
        }
        $files = [];
        foreach (new \DirectoryIterator($dir) as $item) {
            if ($item->isDot() || !$item->isFile()) continue;
            if ($extension && strtolower($item->getExtension()) !== ltrim($extension, '.')) continue;
            $files[] = $item->getPathname();
        }
        return $files;
    }

    // ── Info helpers ──────────────────────────────────────────────────────────

    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    public static function size(string $path): int
    {
        return filesize($path) ?: 0;
    }

    public static function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function extension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    public static function basename(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    public static function ensureDir(string $path, int $mode = 0755): void
    {
        if (!is_dir($path)) {
            mkdir($path, $mode, true);
        }
    }

    public static function sanitizeFilename(string $name): string
    {
        $name = pathinfo($name, PATHINFO_FILENAME);
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        return preg_replace('/_+/', '_', trim($name, '_'));
    }

    private static function uploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File is too large.',
            UPLOAD_ERR_PARTIAL   => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE   => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR=> 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE=> 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension blocked the upload.',
            default              => 'Unknown upload error.',
        };
    }
}
