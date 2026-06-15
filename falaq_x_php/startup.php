<?php

/**
 * FalaqX Framework - Startup / Bootstrap
 * Loads all core components and dispatches the request.
 */

// ── 1. Environment & error reporting ─────────────────────────────────────────
date_default_timezone_set(APP_TIMEZONE);
mb_internal_encoding(APP_CHARSET);

if (APP_DEBUG && APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ── 2. Session ────────────────────────────────────────────────────────────────
ini_set('session.name', SESSION_NAME);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 3. Autoloader ─────────────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $searchPaths = [
        CORE_PATH   . "/{$class}.php",
        CTRL_PATH   . "/{$class}.php",
        MODEL_PATH  . "/{$class}.php",
        HELPER_PATH . "/{$class}.php",
    ];
    foreach ($searchPaths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── 4. Load core files explicitly (order matters) ─────────────────────────────
$coreFiles = [
    'DB',
    'Model',
    'View',
    'Controller',
];
foreach ($coreFiles as $core) {
    require_once CORE_PATH . "/{$core}.php";
}

// ── 5. Load helpers ───────────────────────────────────────────────────────────
$helpers = [
    'Security',
    'Encrypt',
    'Email',
    'Ftp',
    'ImageProcessor',
    'FileHelper',
];
foreach ($helpers as $helper) {
    require_once HELPER_PATH . "/{$helper}.php";
}

// ── 6. Load router and dispatch ───────────────────────────────────────────────
require_once CFG_PATH . '/routes.php';
require_once CORE_PATH . '/Router.php';

$router = new Router($routes);
$router->dispatch();
