<h1><?= $view->e($title) ?></h1>

<div class="card">
    <h2>What is FalaqX?</h2>
    <p style="color:#555; line-height:1.8; margin-top:.6rem;">
        FalaqX is a lightweight PHP MVC framework built for developers who want full
        control without the bloat. It provides all the essential building blocks —
        routing, database access, templating, security, and utility helpers — in a
        clean, easy-to-understand codebase you can read and extend yourself.
    </p>
</div>

<div class="card">
    <h2>Directory Structure</h2>
    <pre style="background:#1a1a2e; color:#e2e2e2; padding:1.5rem; border-radius:6px;
                font-size:.88rem; line-height:1.8; overflow-x:auto; margin-top:.8rem;">
falaqx_framework/
├── index.php                   ← Entry point
├── config.php                  ← Global settings
└── falaq_x_php/
    ├── startup.php             ← Bootstrap / loader
    └── app/
        ├── config/
        │   └── routes.php      ← URL route definitions
        ├── core/
        │   ├── Controller.php  ← Base controller
        │   ├── Model.php       ← Base model
        │   ├── View.php        ← Template engine
        │   ├── DB.php          ← MySQL PDO wrapper
        │   └── Router.php      ← URL dispatcher
        ├── controllers/        ← Your controllers go here
        ├── models/             ← Your models go here
        ├── views/
        │   ├── layouts/        ← Page layouts
        │   ├── shared/         ← Partials (navbar, footer…)
        │   └── …               ← Your view folders
        └── helpers/
            ├── Security.php    ← Sanitize, CSRF, hashing
            ├── Encrypt.php     ← AES-256-GCM encryption
            ├── Email.php       ← SMTP email sender
            ├── Ftp.php         ← FTP file management
            ├── ImageProcessor  ← GD image manipulation
            └── FileHelper.php  ← File copy/upload/delete
    </pre>
</div>

<div class="card">
    <h2>Framework Version</h2>
    <p style="color:#555; margin-top:.5rem;">
        <strong>Version:</strong> <?= APP_VERSION ?><br>
        <strong>Environment:</strong> <?= APP_ENV ?><br>
        <strong>PHP:</strong> <?= PHP_VERSION ?><br>
        <strong>Timezone:</strong> <?= APP_TIMEZONE ?>
    </p>
</div>
