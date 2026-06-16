<?php

namespace FalaqX\Helpers;

/**
 * FalaqX Helper - FTP
 * Upload, download, list, and manage remote files via PHP's ftp_* functions.
 */
class Ftp
{
    /** @var resource|false */
    private $conn = false;
    private bool $connected = false;

    // ── Connection ────────────────────────────────────────────────────────────

    public function connect(
        string $host    = FTP_HOST,
        string $user    = FTP_USER,
        string $pass    = FTP_PASS,
        int    $port    = FTP_PORT,
        bool   $passive = FTP_PASSIVE
    ): self {
        if (!extension_loaded('ftp')) {
            throw new \RuntimeException('PHP FTP extension is not enabled.');
        }

        $this->conn = ftp_connect($host, $port, 30);
        if ($this->conn === false) {
            throw new \RuntimeException("FTP: Cannot connect to {$host}:{$port}");
        }

        if (!ftp_login($this->conn, $user, $pass)) {
            throw new \RuntimeException("FTP: Login failed for user [{$user}].");
        }

        ftp_pasv($this->conn, $passive);
        $this->connected = true;
        return $this;
    }

    public function disconnect(): void
    {
        if ($this->connected && $this->conn) {
            ftp_close($this->conn);
            $this->connected = false;
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    // ── Upload / Download ─────────────────────────────────────────────────────

    /**
     * Upload a local file to the remote server.
     *
     * @param string $localFile   Absolute local path
     * @param string $remotePath  Remote path including filename
     */
    public function upload(string $localFile, string $remotePath): bool
    {
        $this->requireConnection();
        if (!file_exists($localFile)) {
            throw new \RuntimeException("FTP Upload: local file not found [{$localFile}].");
        }
        return ftp_put($this->conn, $remotePath, $localFile, FTP_BINARY);
    }

    /**
     * Download a remote file to a local path.
     */
    public function download(string $remotePath, string $localFile): bool
    {
        $this->requireConnection();
        return ftp_get($this->conn, $localFile, $remotePath, FTP_BINARY);
    }

    // ── Directory operations ──────────────────────────────────────────────────

    public function listFiles(string $remotePath = '.'): array
    {
        $this->requireConnection();
        return ftp_nlist($this->conn, $remotePath) ?: [];
    }

    public function listDetails(string $remotePath = '.'): array
    {
        $this->requireConnection();
        return ftp_rawlist($this->conn, $remotePath) ?: [];
    }

    public function makeDir(string $remotePath): bool
    {
        $this->requireConnection();
        return (bool) ftp_mkdir($this->conn, $remotePath);
    }

    public function changeDir(string $remotePath): bool
    {
        $this->requireConnection();
        return ftp_chdir($this->conn, $remotePath);
    }

    public function currentDir(): string
    {
        $this->requireConnection();
        return ftp_pwd($this->conn);
    }

    // ── File operations ───────────────────────────────────────────────────────

    public function rename(string $oldPath, string $newPath): bool
    {
        $this->requireConnection();
        return ftp_rename($this->conn, $oldPath, $newPath);
    }

    public function delete(string $remotePath): bool
    {
        $this->requireConnection();
        return ftp_delete($this->conn, $remotePath);
    }

    public function fileSize(string $remotePath): int
    {
        $this->requireConnection();
        return ftp_size($this->conn, $remotePath);
    }

    public function chmod(string $remotePath, int $mode = 0644): bool
    {
        $this->requireConnection();
        return (bool) ftp_chmod($this->conn, $mode, $remotePath);
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function requireConnection(): void
    {
        if (!$this->connected || $this->conn === false) {
            throw new RuntimeException('FTP: Not connected. Call connect() first.');
        }
    }
}
