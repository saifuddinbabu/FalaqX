<?php

/**
 * FalaqX Core - DB
 * A thin PDO wrapper. Supports MySQL now; extend for other drivers later.
 * Use as a singleton: DB::getInstance()
 */
class DB
{
    private static ?self $instance = null;
    private PDO $pdo;

    // ── Singleton ─────────────────────────────────────────────────────────────

    private function __construct()
    {
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            DB_DRIVER,
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                throw new RuntimeException('DB Connection failed: ' . $e->getMessage());
            }
            throw new RuntimeException('Database connection error. Please try again later.');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ── Raw PDO access ────────────────────────────────────────────────────────

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    // ── Query helpers ─────────────────────────────────────────────────────────

    /**
     * Run a raw query and return the PDOStatement.
     */
    public function query(string $sql, array $bindings = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    /**
     * Fetch all rows.
     */
    public function fetchAll(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetchAll();
    }

    /**
     * Fetch a single row.
     */
    public function fetchOne(string $sql, array $bindings = []): array|false
    {
        return $this->query($sql, $bindings)->fetch();
    }

    /**
     * Fetch a single column value.
     */
    public function fetchColumn(string $sql, array $bindings = []): mixed
    {
        return $this->query($sql, $bindings)->fetchColumn();
    }

    // ── CRUD shortcuts ────────────────────────────────────────────────────────

    /**
     * Insert a row and return the new ID.
     */
    public function insert(string $table, array $data): int|string
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `{$table}` ({$cols}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }

    /**
     * Update rows matching a WHERE clause.
     *
     * @param array $where  ['column' => value]  (only simple equality for now)
     */
    public function update(string $table, array $data, array $where): int
    {
        $set   = implode(', ', array_map(fn($k) => "`{$k}` = ?", array_keys($data)));
        $cond  = implode(' AND ', array_map(fn($k) => "`{$k}` = ?", array_keys($where)));
        $sql   = "UPDATE `{$table}` SET {$set} WHERE {$cond}";
        $stmt  = $this->query($sql, [...array_values($data), ...array_values($where)]);
        return $stmt->rowCount();
    }

    /**
     * Delete rows matching a WHERE clause.
     */
    public function delete(string $table, array $where): int
    {
        $cond = implode(' AND ', array_map(fn($k) => "`{$k}` = ?", array_keys($where)));
        $sql  = "DELETE FROM `{$table}` WHERE {$cond}";
        $stmt = $this->query($sql, array_values($where));
        return $stmt->rowCount();
    }

    // ── Transactions ──────────────────────────────────────────────────────────

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * Run a callable inside a transaction; rolls back on exception.
     */
    public function transaction(callable $fn): mixed
    {
        $this->beginTransaction();
        try {
            $result = $fn($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    // ── Prevent cloning ───────────────────────────────────────────────────────
    private function __clone() {}
}
