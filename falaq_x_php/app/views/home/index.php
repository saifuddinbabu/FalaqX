<div class="card" style="text-align:center; padding: 3rem 2rem;">
    <div style="font-size:3.5rem; margin-bottom:1rem;">&#9670;</div>
    <h1 style="font-size:2.2rem; margin-bottom:.5rem;"><?= $view->e($title) ?></h1>
    <p style="font-size:1.1rem; color:#666; margin-bottom:2rem;"><?= $view->e($message) ?></p>

    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <a href="<?= APP_URL ?>/users" class="btn btn-primary">&#128100; Manage Users</a>
        <a href="<?= APP_URL ?>/about" class="btn btn-secondary">Learn More</a>
        <a href="<?= APP_URL ?>/contact" class="btn btn-secondary">Contact Us</a>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1.5rem; margin-top:1.5rem;">

    <div class="card">
        <h2>&#128268; MVC Architecture</h2>
        <p style="color:#666; line-height:1.6; margin-top:.5rem;">
            Clean separation of Controllers, Models, and Views.
            Each layer has a single responsibility.
        </p>
    </div>

    <div class="card">
        <h2>&#128737; Secure by Default</h2>
        <p style="color:#666; line-height:1.6; margin-top:.5rem;">
            CSRF protection, AES-256-GCM encryption, bcrypt password hashing,
            and XSS-safe output escaping built in.
        </p>
    </div>

    <div class="card">
        <h2>&#128640; Routing</h2>
        <p style="color:#666; line-height:1.6; margin-top:.5rem;">
            Expressive route definitions with HTTP method matching and
            named URL parameters like <code>{id}</code>.
        </p>
    </div>

    <div class="card">
        <h2>&#128196; DB &amp; ORM</h2>
        <p style="color:#666; line-height:1.6; margin-top:.5rem;">
            PDO-based MySQL layer with transaction support, query helpers,
            and an ActiveRecord-style base Model.
        </p>
    </div>

    <div class="card">
        <h2>&#128247; Image Processing</h2>
        <p style="color:#666; line-height:1.6; margin-top:.5rem;">
            Resize, crop, thumbnail, rotate, flip, greyscale, and watermark
            images using PHP GD — no extra libraries needed.
        </p>
    </div>

    <div class="card">
        <h2>&#9993; Email &amp; FTP</h2>
        <p style="color:#666; line-height:1.6; margin-top:.5rem;">
            Send HTML emails with CC, BCC, attachments, and SMTP support.
            Upload, download, and manage remote files over FTP.
        </p>
    </div>

</div>
