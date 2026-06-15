<div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.2rem;">
    <a href="<?= APP_URL ?>/users/<?= $user['id'] ?>" class="btn btn-secondary btn-sm">&#8592; Back</a>
    <h1 style="margin:0;"><?= $view->e($title) ?></h1>
</div>

<div class="card" style="max-width:500px;">
    <form action="<?= APP_URL ?>/users/<?= $user['id'] ?>" method="POST">
        <?= Security::csrfField() ?>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name"
                   value="<?= $view->e($user['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email"
                   value="<?= $view->e($user['email']) ?>" required>
        </div>

        <div style="display:flex; gap:.8rem; margin-top:.5rem;">
            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="<?= APP_URL ?>/users/<?= $user['id'] ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
