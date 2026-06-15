<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?= APP_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $view->e($title ?? APP_NAME) ?> — <?= APP_NAME ?></title>
    <?= $view->Style('css/style.css') ?>
  
</head>
<body>

<?php $view->partial('shared.navbar') ?>

<?php
// Display any global session flash messages
$flashTypes = ['success', 'error', 'info'];
foreach ($flashTypes as $ft) {
    if (!empty($_SESSION['_flash'][$ft])) {
        echo '<div class="flash ' . $ft . '">' . htmlspecialchars($_SESSION['_flash'][$ft]) . '</div>';
        unset($_SESSION['_flash'][$ft]);
    }
}
?>

<main>
    <?= $content ?>
</main>

<?php $view->partial('shared.footer') ?>

</body>
</html>
