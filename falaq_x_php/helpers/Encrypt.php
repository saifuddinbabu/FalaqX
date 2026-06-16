<?php

namespace FalaqX\Helpers;

/**
 * FalaqX Helper - Encrypt
 * AES-256-GCM symmetric encryption using the APP_KEY constant.
 */
class Encrypt
{
    private const CIPHER   = 'aes-256-gcm';
    private const TAG_LEN  = 16;
    private const IV_LEN   = 12; // 96-bit nonce recommended for GCM

    // ── Encrypt / Decrypt ─────────────────────────────────────────────────────

    /**
     * Encrypt a string.
     * Returns a base64-encoded payload: iv + tag + ciphertext.
     */
    public static function encrypt(string $plaintext, ?string $key = null): string
    {
        $key = self::deriveKey($key ?? APP_KEY);
        $iv  = random_bytes(self::IV_LEN);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LEN
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Encryption failed.');
        }

        return base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * Decrypt a string produced by encrypt().
     */
    public static function decrypt(string $payload, ?string $key = null): string
    {
        $key  = self::deriveKey($key ?? APP_KEY);
        $raw  = base64_decode($payload, true);

        if ($raw === false || strlen($raw) < self::IV_LEN + self::TAG_LEN + 1) {
            throw new \RuntimeException('Invalid encrypted payload.');
        }

        $iv         = substr($raw, 0, self::IV_LEN);
        $tag        = substr($raw, self::IV_LEN, self::TAG_LEN);
        $ciphertext = substr($raw, self::IV_LEN + self::TAG_LEN);

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed: authentication tag mismatch.');
        }

        return $plaintext;
    }

    // ── Hash helpers ──────────────────────────────────────────────────────────

    public static function sha256(string $data): string
    {
        return hash('sha256', $data);
    }

    public static function hmac(string $data, ?string $key = null): string
    {
        return hash_hmac('sha256', $data, $key ?? APP_KEY);
    }

    public static function verifyHmac(string $data, string $hmac, ?string $key = null): bool
    {
        return hash_equals(self::hmac($data, $key), $hmac);
    }

    // ── Base64 URL-safe variants ──────────────────────────────────────────────

    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    // ── Private ───────────────────────────────────────────────────────────────

    /** Derive a 32-byte key from an arbitrary-length secret. */
    private static function deriveKey(string $secret): string
    {
        return hash('sha256', $secret, true); // raw 32 bytes
    }
}
