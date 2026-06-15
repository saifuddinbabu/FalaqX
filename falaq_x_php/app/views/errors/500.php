<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?= APP_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error — <?= APP_NAME ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a2e;
            color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
        }
        .container { max-width: 480px; }
        .code { font-size: 7rem; font-weight: 900; color: #e94560; line-height: 1; }
        h1   { font-size: 1.6rem; margin: .8rem 0; color: #ccc; }
        p    { color: #888; line-height: 1.7; margin-bottom: 2rem; }
        .msg {
            background: #0d0d1a;
            border-left: 4px solid #e94560;
            padding: 1rem 1.2rem;
            text-align: left;
            border-radius: 4px;
            font-family: monospace;
            font-size: .9rem;
            color: #f78b8b;
            margin-bottom: 2rem;
            word-break: break-all;
        }
        a {
            display: inline-block;
            padding: .6rem 1.4rem;
            background: #e94560;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover { opacity: .85; }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">500</div>
        <h1>Internal Server Error</h1>
        <p>Something went wrong on our end. Please try again later.</p>
        <?php if (APP_DEBUG && !empty($errorMessage)): ?>
            <div class="msg"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
        <a href="<?= APP_URL ?>">&#8592; Go Home</a>
    </div>
</body>
</html>
