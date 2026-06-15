<?php

/**
 * FalaqX Core - Router
 * Matches incoming URI to a controller/method and dispatches.
 */
class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $this->sortRoutes($routes);
    }
    private function sortRoutes(array $routes): array {
        // Separate static and dynamic routes
        $static = [];
        $dynamic = [];

        foreach ($routes as $route => $handler) {
            if (strpos($route, '{') === false) {
                $static[$route] = $handler;
            } else {
                $dynamic[$route] = $handler;
            }
        }

        // Merge: static first, then dynamic
        return $static + $dynamic;
    }

    public function dispatch(): void
    {
        $uri    = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $pattern => $handler) {
            // Support "METHOD /path" keys, e.g. "GET /users/{id}"
            [$routeMethod, $routePath] = $this->parsePattern($pattern);

            if ($routeMethod !== '*' && strtoupper($routeMethod) !== $method) {
                continue;
            }

            $params = [];
            if ($this->match($routePath, $uri, $params)) {
                [$controllerName, $action] = $this->resolveHandler($handler);
                $this->callController($controllerName, $action, $params);
                return;
            }
        }

        $this->handle404();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        // Strip base URL path if app is in a sub-directory
        $base = rtrim(parse_url(APP_URL, PHP_URL_PATH) ?? '', '/');
        if ($base && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = parse_url($uri, PHP_URL_PATH);
        return '/' . trim($uri, '/');
    }

    private function parsePattern(string $pattern): array
    {
        if (preg_match('/^(GET|POST|PUT|PATCH|DELETE|ANY)\s+(.+)$/i', trim($pattern), $m)) {
            return [strtoupper($m[1]), rtrim($m[2], '/') ?: '/'];
        }
        // Plain path without method prefix — match any method
        return ['*', rtrim($pattern, '/') ?: '/'];
    }

    private function match(string $routePath, string $uri, array &$params): bool
    {
        // Convert {param} placeholders → named regex groups
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
        $regex = '@^' . $regex . '$@';

        if (preg_match($regex, $uri, $matches)) {
            // Extract only named params
            foreach ($matches as $key => $val) {
                if (is_string($key)) {
                    $params[$key] = $val;
                }
            }
            return true;
        }
        return false;
    }

    private function resolveHandler(string $handler): array
    {
        if (str_contains($handler, '@')) {
            return explode('@', $handler, 2);
        }
        return [$handler, 'index'];
    }

    private function callController(string $controllerName, string $action, array $params): void
    {
        $file = CTRL_PATH . "/{$controllerName}.php";

        if (!file_exists($file)) {
            $this->handle404("Controller [{$controllerName}] not found.");
            return;
        }

        require_once $file;

        if (!class_exists($controllerName)) {
            $this->handle404("Class [{$controllerName}] not defined.");
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            $this->handle404("Method [{$action}] not found in [{$controllerName}].");
            return;
        }

        call_user_func_array([$controller, $action], array_values($params));
    }

    private function handle404(string $message = 'Page Not Found'): void
    {
        http_response_code(404);

        $view404 = VIEW_PATH . '/errors/404.php';
        if (file_exists($view404)) {
            include $view404;
        } else {
            echo "<h1>404 — {$message}</h1>";
        }
    }
}
