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

// ── 3. PSR-4 Namespace Autoloader ─────────────────────────────────────────────
//
//  Namespace map:
//    FalaqX\Core\*      →  falaq_x_php/app/core/
//    FalaqX\Helpers\*   →  falaq_x_php/helpers/
//    App\Controllers\*  →  falaq_x_php/app/controllers/
//    App\Models\*       →  falaq_x_php/app/models/
//
spl_autoload_register(function (string $class): void {
    $namespaceMap = [
        'FalaqX\\Core\\'      => CORE_PATH,
        'FalaqX\\Helpers\\'   => HELPER_PATH,
        'App\\Controllers\\'  => CTRL_PATH,
        'App\\Models\\'       => MODEL_PATH,
    ];

    foreach ($namespaceMap as $prefix => $baseDir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . DIRECTORY_SEPARATOR
                  . str_replace('\\', DIRECTORY_SEPARATOR, $relative)
                  . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// ── 4. Load core files explicitly (order matters) ─────────────────────────────
$coreFiles = ['DB', 'Model', 'View', 'Controller'];
foreach ($coreFiles as $core) {
    require_once CORE_PATH . "/{$core}.php";
}

// ── 5. Load helpers ───────────────────────────────────────────────────────────
$helpers = ['Security', 'Encrypt', 'Email', 'Ftp', 'ImageProcessor', 'FileHelper'];
foreach ($helpers as $helper) {
    require_once HELPER_PATH . "/{$helper}.php";
}

// ── 6. Global class aliases ───────────────────────────────────────────────────
// Allows view files to call Security::, Encrypt::, etc. without use statements.
class_alias(\FalaqX\Helpers\Security::class,       'Security');
class_alias(\FalaqX\Helpers\Encrypt::class,        'Encrypt');
class_alias(\FalaqX\Helpers\Email::class,          'Email');
class_alias(\FalaqX\Helpers\Ftp::class,            'Ftp');
class_alias(\FalaqX\Helpers\ImageProcessor::class, 'ImageProcessor');
class_alias(\FalaqX\Helpers\FileHelper::class,     'FileHelper');

// ── 7. Load router and dispatch ───────────────────────────────────────────────
require_once CFG_PATH . '/routes.php';
require_once CORE_PATH . '/Router.php';

$router = new \FalaqX\Core\Router($routes);
$router->dispatch();