<?php

require_once('Database.php');

class QueryBuilder
{
    private string $model;
    private string $table;
    private array  $wheres   = [];
    private array  $orderBys = [];
    private array  $bindings = [];
    private ?int   $limitVal  = null;
    private ?int   $offsetVal = null;

    private static array $allowedOperators = [
        '=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE',
    ];

    public function __construct(string $model, string $table)
    {
        $this->model = $model;
        $this->table = $table;
    }

    public function where(string $column, $operator, $value = null): self
    {
        if ($value === null) {
            $value    = $operator;
            $operator = '=';
        }

        $operator = strtoupper($operator);
        if (!in_array($operator, self::$allowedOperators, true)) {
            throw new InvalidArgumentException("Operator '{$operator}' is not allowed.");
        }

        $this->wheres[]   = "{$column} {$operator} ?";
        $this->bindings[] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction      = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBys[] = "{$column} {$direction}";

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limitVal = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offsetVal = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        if (!empty($this->orderBys)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBys);
        }
        if ($this->limitVal !== null) {
            $sql .= " LIMIT {$this->limitVal}";
        }
        if ($this->offsetVal !== null) {
            $sql .= " OFFSET {$this->offsetVal}";
        }

        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute($this->bindings);

        $model = $this->model;
        return array_map(fn($row) => new $model($row), $stmt->fetchAll());
    }

    public function first()
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute($this->bindings);

        return (int) $stmt->fetch()['count'];
    }
}
