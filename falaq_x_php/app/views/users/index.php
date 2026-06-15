<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.2rem;">
    <h1><?= $view->e($title) ?></h1>
    <a href="<?= APP_URL ?>/users/create" class="btn btn-primary">+ New User</a>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <?php if (empty($users)): ?>
        <p style="padding:2rem; color:#888; text-align:center;">No users found. <a href="<?= APP_URL ?>/users/create">Create one</a>.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $view->e($user['id']) ?></td>
                    <td><?= $view->e($user['name']) ?></td>
                    <td><?= $view->e($user['email']) ?></td>
                    <td>
                        <div class="actions">
                            <a href="<?= APP_URL ?>/users/<?= $user['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                            <a href="<?= APP_URL ?>/users/<?= $user['id'] ?>/edit" class="btn btn-primary btn-sm">Edit</a>
                            <form action="<?= APP_URL ?>/users/<?= $user['id'] ?>/delete" method="POST"
                                  onsubmit="return confirm('Delete this user?')">
                                <?= Security::csrfField() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
