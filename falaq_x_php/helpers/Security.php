<?php

namespace FalaqX\Helpers;

/**
 * FalaqX Helper - Security
 * Input sanitization, CSRF tokens, password hashing, XSS prevention.
 */
class Security
{
    // ── Input sanitization ────────────────────────────────────────────────────

    /** Strip tags and trim a string. */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES | ENT_SUBSTITUTE, APP_CHARSET);
    }

    /** Sanitize an array of inputs recursively. */
    public static function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = is_array($value)
                ? self::sanitizeArray($value)
                : self::sanitize((string) $value);
        }
        return $data;
    }

    /** Escape HTML entities for safe output. */
    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, APP_CHARSET);
    }

    // ── Password hashing ──────────────────────────────────────────────────────

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_ROUNDS]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => BCRYPT_ROUNDS]);
    }

    // ── Token generation ──────────────────────────────────────────────────────

    /** Generate a cryptographically secure random hex token. */
    public static function generateToken(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }

    /** Timing-safe string comparison (prevents timing attacks). */
    public static function compareHash(string $a, string $b): bool
    {
        return hash_equals($a, $b);
    }

    // ── CSRF helpers ──────────────────────────────────────────────────────────

    public static function csrfToken(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = self::generateToken();
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /** Output a hidden CSRF input field (call inside HTML forms). */
    public static function csrfField(): string
    {
        $token = self::csrfToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }

    public static function verifyCsrf(string $token): bool
    {
        return self::compareHash($token, $_SESSION[CSRF_TOKEN_NAME] ?? '');
    }

    // ── Validation helpers ────────────────────────────────────────────────────

    public static function isEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function isAlphanumeric(string $str): bool
    {
        return ctype_alnum($str);
    }

    public static function isNumeric(mixed $value): bool
    {
        return is_numeric($value);
    }

    /** Check minimum/maximum string length. */
    public static function length(string $str, int $min = 0, int $max = PHP_INT_MAX): bool
    {
        $len = mb_strlen($str, APP_CHARSET);
        return $len >= $min && $len <= $max;
    }

    // ── IP & rate-limit helpers ───────────────────────────────────────────────

    public static function getClientIp(): string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'REMOTE_ADDR',
        ];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }
}
