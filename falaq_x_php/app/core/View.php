<?php

namespace FalaqX\Core;
/**
 * FalaqX Core - View
 * Renders template files with optional layout wrapping.
 */
class View
{
    private string $content = '';

    /**
     * Render a template, optionally wrapped in a layout.
     *
     * @param string $template  Dot-notation: 'home.index' → views/home/index.php
     * @param array  $data      Data variables available inside the template
     * @param string $layout    Layout name inside views/layouts/ ('' = none)
     */    
    public function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $templateFile = $this->resolvePath($template);

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View [{$template}] not found at {$templateFile}");
        }

        // Render the inner template
        $this->content = $this->capture($templateFile, $data);

        if ($layout !== '' && $layout !== 'none') {
            $layoutFile = VIEW_PATH . "/layouts/{$layout}.php";
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout [{$layout}] not found at {$layoutFile}");
            }
            // $content is available as $content inside the layout
            echo $this->capture($layoutFile, array_merge($data, ['content' => $this->content]));
        } else {
            echo $this->content;
        }
    }

    /**
     * Render a partial (sub-template) — call from within a template.
     *
     * @param string $partial  Dot-notation path, e.g. 'shared.navbar'
     * @param array  $data
     */
    public function partial(string $partial, array $data = []): void
    {
        $file = $this->resolvePath($partial);
        if (!file_exists($file)) {
            throw new \RuntimeException("Partial [{$partial}] not found.");
        }
        echo $this->capture($file, $data);
    }

    /**
     * Escape a string for safe HTML output.
     */
    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, APP_CHARSET);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function resolvePath(string $template): string
    {
        // Convert dot notation to directory separator
        $relative = str_replace('.', DIRECTORY_SEPARATOR, $template) . '.php';
        return VIEW_PATH . DIRECTORY_SEPARATOR . $relative;
    }

    /**
     * Include a PHP file in an isolated scope, capturing its output.
     */
    private function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        $view = $this; // make $view available in templates for partials / e()
        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Create an HTML <link> tag with optional attributes.
     *
     * This function generates a <link> element string using the provided
     * relationship, type, and href values. Only non-empty parameters are included
     * in the output to avoid undefined variable errors.
     *
     * @param string $rel   The relationship of the linked resource (e.g., "stylesheet", "icon").
     * @param string $type  The MIME type of the linked resource (e.g., "text/css", "image/png").
     * @param string $href  The URL or path to the linked resource.
     *
     * @return string       The generated <link> tag as a string.
     */
    public function Link(string $rel, string $type, string $href): string
    {
        $attributes = [];

        if (!empty($rel)) {
            $attributes[] = 'rel="' . htmlspecialchars($rel, ENT_QUOTES) . '"';
        }
        if (!empty($type)) {
            $attributes[] = 'type="' . htmlspecialchars($type, ENT_QUOTES) . '"';
        }
        if (!empty($href)) {
            $attributes[] = 'href="' . htmlspecialchars($href, ENT_QUOTES) . '"';
        }

        return '<link ' . implode(' ', $attributes) . '>';
    }

    /**
     * Create an HTML <link> tag with style only.
     *
     * @param string $filePath  path relative to assets directory .
     *
     * @return string       The generated <link> tag as a string.
     */

    public function Style(string $filePath): string{
        return $this->Link("stylesheet","",APP_ASSETS_URL . $filePath);
    }

    /**
     * Create an HTML <script> tag with optional attributes.
     *
     * Generates a <script> element string using the provided type, src, async, and defer values.
     * Only non-empty parameters are included in the output to avoid undefined variable errors.
     *
     * @param string $type  The MIME type of the script (e.g., "text/javascript").
     * @param string $src   The URL or path to the external script file.
     * @param bool   $async Whether to add the async attribute (true = include).
     * @param bool   $defer Whether to add the defer attribute (true = include).
     *
     * @return string       The generated <script> tag as a string.
     */
    public function Script(string $type, string $src, bool $async = false, bool $defer = false): string
    {
        $attributes = [];

        if (!empty($type)) {
            $attributes[] = 'type="' . htmlspecialchars($type, ENT_QUOTES) . '"';
        }
        if (!empty($src)) {
            $attributes[] = 'src="' . htmlspecialchars($src, ENT_QUOTES) . '"';
        }
        if ($async) {
            $attributes[] = 'async';
        }
        if ($defer) {
            $attributes[] = 'defer';
        }

        return '<script ' . implode(' ', $attributes) . '></script>';
    }


    /**
     * Create an HTML <meta> tag with optional attributes.
     *
     * Generates a <meta> element string using the provided name, content, and charset values.
     * Only non-empty parameters are included in the output to avoid undefined variable errors.
     *
     * @param string $name    The name of the meta information (e.g., "description", "keywords").
     * @param string $content The content value for the meta tag.
     * @param string $charset The character encoding (e.g., "UTF-8").
     *
     * @return string         The generated <meta> tag as a string.
     */
    public function Meta(string $name, string $content, string $charset = ''): string
    {
        $attributes = [];

        if (!empty($name)) {
            $attributes[] = 'name="' . htmlspecialchars($name, ENT_QUOTES) . '"';
        }
        if (!empty($content)) {
            $attributes[] = 'content="' . htmlspecialchars($content, ENT_QUOTES) . '"';
        }
        if (!empty($charset)) {
            $attributes[] = 'charset="' . htmlspecialchars($charset, ENT_QUOTES) . '"';
        }

        return '<meta ' . implode(' ', $attributes) . '>';
    }




}
