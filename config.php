<?php

/**
 * FalaqX Framework - Global Configuration
 * Edit this file to configure your application settings.
 */

// ─────────────────────────────────────────────
// APPLICATION SETTINGS
// ─────────────────────────────────────────────
define('APP_NAME',    'FalaqX App');
define('APP_VERSION', '1.0.0');
define('APP_ENV',     'development'); // 'development' | 'production'
define('APP_DEBUG',   true);
define('APP_URL',     'http://localhost/falaqx_framework');
define('APP_CHARSET', 'UTF-8');
define('APP_TIMEZONE','Asia/Dhaka');

// ─────────────────────────────────────────────
// DATABASE SETTINGS
// ─────────────────────────────────────────────
define('DB_DRIVER',   'mysql');
define('DB_HOST',     'localhost');
define('DB_PORT',     3306);
define('DB_NAME',     'falaqx_db');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_CHARSET',  'utf8mb4');

// ─────────────────────────────────────────────
// SECURITY SETTINGS
// ─────────────────────────────────────────────
define('APP_KEY',        'change-this-to-a-random-32-char-string!!'); // Used for encryption
define('BCRYPT_ROUNDS',  12);
define('SESSION_NAME',   'falaqx_session');
define('CSRF_TOKEN_NAME','_csrf_token');

// ─────────────────────────────────────────────
// EMAIL SETTINGS (SMTP)
// ─────────────────────────────────────────────
define('MAIL_HOST',       'smtp.mailtrap.io');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   '');
define('MAIL_PASSWORD',   '');
define('MAIL_ENCRYPTION', 'tls');   // 'tls' | 'ssl' | ''
define('MAIL_FROM_NAME',  APP_NAME);
define('MAIL_FROM_EMAIL', 'no-reply@example.com');

// ─────────────────────────────────────────────
// FTP SETTINGS
// ─────────────────────────────────────────────
define('FTP_HOST',    'ftp.example.com');
define('FTP_USER',    '');
define('FTP_PASS',    '');
define('FTP_PORT',    21);
define('FTP_PASSIVE', true);

// ─────────────────────────────────────────────
// FILE / UPLOAD SETTINGS
// ─────────────────────────────────────────────
define('UPLOAD_PATH',     ROOT_PATH . '/public/uploads');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg','jpeg','png','gif','pdf','docx','xlsx','zip']);

// ─────────────────────────────────────────────
// IMAGE PROCESSING DEFAULTS
// ─────────────────────────────────────────────
define('IMAGE_QUALITY',   85);  // JPEG quality (0-100)
define('THUMB_WIDTH',     200);
define('THUMB_HEIGHT',    200);

// ─────────────────────────────────────────────
// PATH CONSTANTS (do not change)
// ─────────────────────────────────────────────
define('APP_PATH',      FALAQ_PATH . '/app');
define('CORE_PATH',     APP_PATH   . '/core');
define('CTRL_PATH',     APP_PATH   . '/controllers');
define('MODEL_PATH',    APP_PATH   . '/models');
define('VIEW_PATH',     APP_PATH   . '/views');
define('HELPER_PATH',   FALAQ_PATH . '/helpers');
define('CFG_PATH',      APP_PATH   . '/config');
