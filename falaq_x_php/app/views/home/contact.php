<h1><?= $view->e($title) ?></h1>

<?php if (!empty($flash)): ?>
    <div class="flash success"><?= $view->e($flash) ?></div>
<?php endif; ?>

<div class="card" style="max-width:600px;">
    <form action="<?= APP_URL ?>/contact" method="POST">
        <?= Security::csrfField() ?>

        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" placeholder="John Doe" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="john@example.com" required>
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Write your message here…" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">&#9993; Send Message</button>
    </form>
</div>
