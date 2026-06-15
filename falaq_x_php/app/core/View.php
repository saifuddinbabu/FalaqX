<?php

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
            throw new RuntimeException("View [{$template}] not found at {$templateFile}");
        }

        // Render the inner template
        $this->content = $this->capture($templateFile, $data);

        if ($layout !== '' && $layout !== 'none') {
            $layoutFile = VIEW_PATH . "/layouts/{$layout}.php";
            if (!file_exists($layoutFile)) {
                throw new RuntimeException("Layout [{$layout}] not found at {$layoutFile}");
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
            throw new RuntimeException("Partial [{$partial}] not found.");
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
}
