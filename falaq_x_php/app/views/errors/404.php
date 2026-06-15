<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?= APP_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found — <?= APP_NAME ?></title>
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
        .code {
            font-size: 7rem;
            font-weight: 900;
            color: #e94560;
            line-height: 1;
            letter-spacing: -4px;
        }
        h1 { font-size: 1.6rem; margin: .8rem 0; color: #ccc; }
        p  { color: #888; line-height: 1.7; margin-bottom: 2rem; }
        a  {
            display: inline-block;
            padding: .6rem 1.4rem;
            background: #e94560;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: opacity .2s;
        }
        a:hover { opacity: .85; }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">404</div>
        <h1>Page Not Found</h1>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <a href="<?= APP_URL ?>">&#8592; Go Home</a>
    </div>
</body>
</html>
