<?php

/**
 * FalaqX Core - Base Controller
 * All user controllers extend this class.
 */
class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    // ── View helpers ──────────────────────────────────────────────────────────

    /**
     * Render a view file.
     *
     * @param string $template  Dot-notation path relative to views/, e.g. 'home.index'
     * @param array  $data      Variables to extract into the template scope
     * @param string $layout    Layout file name inside views/layouts/ (empty = no layout)
     */
    protected function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $this->view->render($template, $data, $layout);
    }

    /**
     * Return JSON response and terminate.
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=' . APP_CHARSET);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // ── Redirect ──────────────────────────────────────────────────────────────

    protected function redirect(string $url, int $code = 302): void
    {
        header("Location: {$url}", true, $code);
        exit;
    }

    protected function redirectTo(string $path, int $code = 302): void
    {
        $this->redirect(APP_URL . '/' . ltrim($path, '/'), $code);
    }

    // ── Request helpers ───────────────────────────────────────────────────────

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    // ── Session helpers ───────────────────────────────────────────────────────

    protected function setSession(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    protected function getSession(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    protected function removeSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    // ── Flash messages ────────────────────────────────────────────────────────

    protected function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    protected function getFlash(string $type): ?string
    {
        $msg = $_SESSION['_flash'][$type] ?? null;
        unset($_SESSION['_flash'][$type]);
        return $msg;
    }

    // ── CSRF ──────────────────────────────────────────────────────────────────

    protected function generateCsrf(): string
    {
        $token = Security::generateToken();
        $_SESSION[CSRF_TOKEN_NAME] = $token;
        return $token;
    }

    protected function verifyCsrf(): bool
    {
        $token = $this->post(CSRF_TOKEN_NAME) ?? $this->get(CSRF_TOKEN_NAME);
        return Security::compareHash($token ?? '', $_SESSION[CSRF_TOKEN_NAME] ?? '');
    }

    // ── Model loader ──────────────────────────────────────────────────────────

    protected function model(string $modelName): object
    {
        $file = MODEL_PATH . "/{$modelName}.php";
        if (!file_exists($file)) {
            throw new RuntimeException("Model [{$modelName}] not found.");
        }
        require_once $file;
        return new $modelName();
    }
}
