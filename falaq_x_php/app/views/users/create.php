<div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.2rem;">
    <a href="<?= APP_URL ?>/users" class="btn btn-secondary btn-sm">&#8592; Back</a>
    <h1 style="margin:0;"><?= $view->e($title) ?></h1>
</div>

<div class="card" style="max-width:500px;">
    <form action="<?= APP_URL ?>/users" method="POST">
        <?= Security::csrfField() ?>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Jane Doe" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="jane@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Min. 8 characters" required minlength="8">
        </div>

        <div style="display:flex; gap:.8rem; margin-top:.5rem;">
            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="<?= APP_URL ?>/users" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
