<?php

namespace FalaqX\Core;

/**
 * FalaqX Core - Base Model
 * Provides ActiveRecord-style helpers on top of DB.
 * User models extend this and set $table / $primaryKey.
 */
class Model
{
    /** @var string  Database table name — override in each model */
    protected string $table = '';

    /** @var string  Primary key column */
    protected string $primaryKey = 'id';

    /** @var array  Columns that may be mass-assigned */
    protected array $fillable = [];

    protected DB $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    // ── Basic finders ─────────────────────────────────────────────────────────

    public function all(string $orderBy = ''): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->fetchAll($sql);
    }

    public function find(int|string $id): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    public function findBy(string $column, mixed $value): array|false
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1",
            [$value]
        );
    }

    public function where(string $column, mixed $value): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ?",
            [$value]
        );
    }

    public function count(): int
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM `{$this->table}`");
    }

    // ── Write operations ──────────────────────────────────────────────────────

    public function create(array $data): int|string
    {
        $data = $this->filterFillable($data);
        return $this->db->insert($this->table, $data);
    }

    public function update(int|string $id, array $data): int
    {
        $data = $this->filterFillable($data);
        return $this->db->update($this->table, $data, [$this->primaryKey => $id]);
    }

    public function delete(int|string $id): int
    {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }

    // ── Raw query passthrough ─────────────────────────────────────────────────

    public function query(string $sql, array $bindings = []): \PDOStatement
    {
        return $this->db->query($sql, $bindings);
    }

    public function fetchAll(string $sql, array $bindings = []): array
    {
        return $this->db->fetchAll($sql, $bindings);
    }

    public function fetchOne(string $sql, array $bindings = []): array|false
    {
        return $this->db->fetchOne($sql, $bindings);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data; // no restriction — use with care
        }
        return array_filter(
            $data,
            fn($key) => in_array($key, $this->fillable, true),
            ARRAY_FILTER_USE_KEY
        );
    }
}
