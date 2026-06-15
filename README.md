# FalaqX PHP Framework

A lightweight, hand-crafted PHP MVC framework. No Composer, no dependencies — just clean PHP 8.1+.

---

## Quick Start

### 1. Requirements
- PHP 8.1+
- Apache with `mod_rewrite` enabled (or Nginx — see below)
- MySQL 5.7+ / MariaDB 10.3+
- PHP extensions: `pdo_mysql`, `gd`, `ftp`, `mbstring`, `openssl`

### 2. Installation
```bash
# Place the project in your web root, e.g.:
/var/www/html/falaqx_framework/

# Or run with PHP's built-in server (from project root):
php -S localhost:8000
```

### 3. Configure
Edit **`config.php`** in the project root:
```php
define('APP_URL',  'http://localhost/falaqx_framework');
define('DB_NAME',  'your_database');
define('DB_USER',  'root');
define('DB_PASS',  'your_password');
define('APP_KEY',  'change-this-to-a-random-32-char-string!!');
```

### 4. Create the database
```sql
CREATE DATABASE falaqx_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE falaqx_db;

CREATE TABLE users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    email      VARCHAR(180) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    status     ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## Directory Structure

```
falaqx_framework/
├── .htaccess                       ← Apache rewrite rules
├── index.php                       ← Entry point (do not rename)
├── config.php                      ← All application settings
└── falaq_x_php/
    ├── startup.php                 ← Bootstraps the framework
    └── app/
        ├── config/
        │   └── routes.php          ← URL → Controller@method map
        ├── core/
        │   ├── Router.php          ← Dispatches requests
        │   ├── Controller.php      ← Base controller (extend this)
        │   ├── Model.php           ← Base model (extend this)
        │   ├── View.php            ← Template renderer
        │   └── DB.php              ← MySQL PDO singleton
        ├── controllers/            ← Your controllers
        ├── models/                 ← Your models
        ├── views/
        │   ├── layouts/main.php    ← Default HTML layout
        │   ├── shared/             ← Navbar, footer partials
        │   └── errors/             ← 404, 500 error pages
        └── helpers/
            ├── Security.php        ← Sanitize, CSRF, hashing, validation
            ├── Encrypt.php         ← AES-256-GCM encryption / HMAC
            ├── Email.php           ← HTML email sender
            ├── Ftp.php             ← FTP client
            ├── ImageProcessor.php  ← GD image manipulation
            └── FileHelper.php      ← File upload / copy / delete
```

---

## Routing (`app/config/routes.php`)

```php
$routes = [
    'GET /'              => 'HomeController@index',
    'GET /posts/{id}'    => 'PostController@show',
    'POST /posts'        => 'PostController@store',
    'GET /posts/{id}/edit' => 'PostController@edit',
];
```

---

## Creating a Controller

```php
// falaq_x_php/app/controllers/PostController.php
class PostController extends Controller
{
    public function index(): void
    {
        $model = $this->model('PostModel');
        $posts = $model->all('created_at DESC');
        $this->render('posts.index', ['posts' => $posts]);
    }

    public function show(string $id): void
    {
        $post = $this->model('PostModel')->find((int) $id);
        $this->render('posts.show', ['post' => $post]);
    }
}
```

---

## Creating a Model

```php
// falaq_x_php/app/models/PostModel.php
class PostModel extends Model
{
    protected string $table    = 'posts';
    protected array  $fillable = ['title', 'body', 'user_id'];

    public function published(): array
    {
        return $this->where('status', 'published');
    }
}
```

---

## Views

Views live in `app/views/` using **dot-notation**:

| Dot path         | File path                          |
|------------------|------------------------------------|
| `'posts.index'`  | `app/views/posts/index.php`        |
| `'posts.show'`   | `app/views/posts/show.php`         |

Inside a view, `$view->e($val)` escapes HTML output safely.
Use `$view->partial('shared.navbar')` to include sub-templates.

---

## Helpers

### Security
```php
Security::sanitize($userInput);
Security::hashPassword($plain);
Security::verifyPassword($plain, $hash);
Security::csrfField();         // outputs hidden <input>
Security::isEmail($email);
```

### Encryption
```php
$token = Encrypt::encrypt('secret data');
$plain = Encrypt::decrypt($token);
$hmac  = Encrypt::hmac('data');
```

### Email
```php
(new Email())
    ->to('user@example.com', 'User Name')
    ->subject('Hello!')
    ->body('<p>Welcome!</p>')
    ->attach('/path/to/file.pdf')
    ->send();
```

### Image Processing
```php
(new ImageProcessor())
    ->load('/uploads/photo.jpg')
    ->thumbnail(300, 300)
    ->watermarkText('FalaqX', 'bottom-right')
    ->save('/uploads/thumb.jpg');
```

### File Helper
```php
$result = FileHelper::upload('avatar', UPLOAD_PATH);
FileHelper::copy('/src/file.txt', '/dest/file.txt');
echo FileHelper::humanSize(FileHelper::size('/file.txt'));
```

### FTP
```php
$ftp = (new Ftp())->connect();
$ftp->upload('/local/file.zip', '/remote/file.zip');
$ftp->disconnect();
```

---

## Nginx Configuration

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ ^/falaq_x_php/ {
    deny all;
}
```

---

## License
GNU General Public License Version 3.
