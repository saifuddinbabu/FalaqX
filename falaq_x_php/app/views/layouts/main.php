<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?= APP_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $view->e($title ?? APP_NAME) ?> — <?= APP_NAME ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ─────────────────────────────────────── */
        nav {
            background: #1a1a2e;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }
        nav .brand {
            color: #e94560;
            font-size: 1.4rem;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 1px;
        }
        nav ul { list-style: none; display: flex; gap: 1.5rem; }
        nav ul a {
            color: #ccc;
            text-decoration: none;
            font-size: .95rem;
            transition: color .2s;
        }
        nav ul a:hover { color: #e94560; }

        /* ── Flash messages ──────────────────────────────── */
        .flash {
            padding: .85rem 1.5rem;
            margin: 1rem 2rem 0;
            border-radius: 6px;
            font-size: .95rem;
        }
        .flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash.error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .flash.info    { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

        /* ── Main content ────────────────────────────────── */
        main {
            flex: 1;
            max-width: 1100px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        h1 { font-size: 1.8rem; color: #1a1a2e; margin-bottom: 1.2rem; }
        h2 { font-size: 1.4rem; color: #1a1a2e; margin-bottom: 1rem; }

        /* ── Cards ───────────────────────────────────────── */
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            margin-bottom: 1.5rem;
        }

        /* ── Buttons ─────────────────────────────────────── */
        .btn {
            display: inline-block;
            padding: .55rem 1.2rem;
            border-radius: 5px;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: opacity .2s, transform .1s;
        }
        .btn:hover { opacity: .88; transform: translateY(-1px); }
        .btn-primary  { background: #e94560; color: #fff; }
        .btn-secondary{ background: #1a1a2e; color: #fff; }
        .btn-success  { background: #28a745; color: #fff; }
        .btn-danger   { background: #dc3545; color: #fff; }
        .btn-sm { padding: .35rem .8rem; font-size: .82rem; }

        /* ── Forms ───────────────────────────────────────── */
        .form-group { margin-bottom: 1.2rem; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: .4rem;
            font-size: .9rem;
            color: #555;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: .6rem .9rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: .95rem;
            transition: border-color .2s;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #e94560;
        }
        .form-group textarea { resize: vertical; min-height: 120px; }

        /* ── Table ───────────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .8rem 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #1a1a2e; color: #fff; font-size: .88rem; text-transform: uppercase; letter-spacing: .5px; }
        tr:hover td { background: #f9f9f9; }
        td .actions { display: flex; gap: .5rem; }

        /* ── Footer ──────────────────────────────────────── */
        footer {
            background: #1a1a2e;
            color: #888;
            text-align: center;
            padding: 1rem;
            font-size: .85rem;
        }
        footer span { color: #e94560; }
    </style>
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
