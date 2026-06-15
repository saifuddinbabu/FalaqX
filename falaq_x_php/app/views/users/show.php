<div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.2rem;">
    <a href="<?= APP_URL ?>/users" class="btn btn-secondary btn-sm">&#8592; Back</a>
    <h1 style="margin:0;"><?= $view->e($user['name']) ?></h1>
</div>

<div class="card" style="max-width:500px;">
    <table style="border:none;">
        <tr>
            <th style="background:none; color:#1a1a2e; width:120px;">ID</th>
            <td><?= $view->e($user['id']) ?></td>
        </tr>
        <tr>
            <th style="background:none; color:#1a1a2e;">Name</th>
            <td><?= $view->e($user['name']) ?></td>
        </tr>
        <tr>
            <th style="background:none; color:#1a1a2e;">Email</th>
            <td><?= $view->e($user['email']) ?></td>
        </tr>
        <?php if (!empty($user['created_at'])): ?>
        <tr>
            <th style="background:none; color:#1a1a2e;">Created</th>
            <td><?= $view->e($user['created_at']) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div style="display:flex; gap:.8rem; margin-top:1.5rem;">
        <a href="<?= APP_URL ?>/users/<?= $user['id'] ?>/edit" class="btn btn-primary">Edit</a>
        <form action="<?= APP_URL ?>/users/<?= $user['id'] ?>/delete" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this user?')">
            <?= Security::csrfField() ?>
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
