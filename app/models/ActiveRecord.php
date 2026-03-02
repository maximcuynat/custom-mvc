<?php

namespace App\Models;

abstract class ActiveRecord
{
    protected static $table;
    protected static $primaryKey = 'id';
    protected static bool $timestamps = true;

    protected $attributes = [];
    protected $original = [];
    protected $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        if (!empty($attributes)) {
            $this->exists = true;
            $this->original = $attributes;
        }
    }

    protected static function getTable(): string
    {
        if (isset(static::$table)) {
            return static::$table;
        }
        return static::pluralize(
            strtolower((new \ReflectionClass(static::class))->getShortName())
        );
    }

    private static function pluralize(string $word): string
    {
        if (preg_match('/[^aeiou]y$/i', $word)) {
            return substr($word, 0, -1) . 'ies';   // category → categories
        }
        if (preg_match('/(s|x|z|ch|sh)$/i', $word)) {
            return $word . 'es';                    // class → classes
        }
        return $word . 's';                         // user → users
    }

    protected static function db()
    {
        return Database::getInstance()->getConnection();
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public static function all()
    {
        $table = static::getTable();
        $stmt = static::db()->query("SELECT * FROM {$table}");
        $results = $stmt->fetchAll();
        
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }

    public static function find($id)
    {
        $table = static::getTable();
        $pk = static::$primaryKey;
        
        $stmt = static::db()->prepare("SELECT * FROM {$table} WHERE {$pk} = ? LIMIT 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        return $result ? new static($result) : null;
    }

    public static function findOrFail($id)
    {
        $result = static::find($id);
        if (!$result) {
            throw new Exception(static::class . " with ID {$id} not found");
        }
        return $result;
    }

    public static function where(string $column, $operator, $value = null): QueryBuilder
    {
        return (new QueryBuilder(static::class, static::getTable()))
            ->where($column, $operator, $value);
    }

    public static function first()
    {
        $table = static::getTable();
        $stmt = static::db()->query("SELECT * FROM {$table} LIMIT 1");
        $result = $stmt->fetch();
        
        return $result ? new static($result) : null;
    }

    public static function count()
    {
        $table = static::getTable();
        $stmt = static::db()->query("SELECT COUNT(*) as count FROM {$table}");
        return (int) $stmt->fetch()['count'];
    }

    public function save()
    {
        if ($this->exists) {
            return $this->update();
        }
        return $this->insert();
    }

    protected function insert()
    {
        $table = static::getTable();
        $attributes = $this->attributes;

        if (static::$timestamps) {
            $now = date('Y-m-d H:i:s');
            $attributes['created_at'] = $attributes['created_at'] ?? $now;
            $attributes['updated_at'] = $attributes['updated_at'] ?? $now;
        }

        $columns = array_keys($attributes);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = static::db()->prepare($sql);
        $stmt->execute(array_values($attributes));

        $this->attributes[static::$primaryKey] = static::db()->lastInsertId();
        $this->attributes['created_at'] = $attributes['created_at'] ?? null;
        $this->attributes['updated_at'] = $attributes['updated_at'] ?? null;
        $this->exists = true;
        $this->original = $this->attributes;

        return $this;
    }

    protected function update()
    {
        $table = static::getTable();
        $pk = static::$primaryKey;
        $attributes = $this->attributes;

        unset($attributes[$pk]);

        if (static::$timestamps) {
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }

        $setClause = [];
        foreach (array_keys($attributes) as $column) {
            $setClause[] = "{$column} = ?";
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $setClause) . " WHERE {$pk} = ?";

        $values = array_values($attributes);
        $values[] = $this->attributes[$pk];

        $stmt = static::db()->prepare($sql);
        $stmt->execute($values);

        $this->attributes = array_merge($this->attributes, $attributes);
        $this->original = $this->attributes;

        return $this;
    }

    public function delete()
    {
        if (!$this->exists) {
            throw new Exception("Cannot delete a model that doesn't exist");
        }
        
        $table = static::getTable();
        $pk = static::$primaryKey;
        
        $stmt = static::db()->prepare("DELETE FROM {$table} WHERE {$pk} = ?");
        $stmt->execute([$this->attributes[$pk]]);
        
        $this->exists = false;
        
        return true;
    }

    public static function destroy($id)
    {
        $model = static::find($id);
        if ($model) {
            return $model->delete();
        }
        return false;
    }

    public static function create(array $attributes)
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function toJson()
    {
        return json_encode($this->attributes);
    }

    public function isDirty()
    {
        return $this->attributes !== $this->original;
    }

    public function getOriginal()
    {
        return $this->original;
    }

    public function refresh()
    {
        if (!$this->exists) {
            throw new Exception("Cannot refresh a model that doesn't exist");
        }
        
        $fresh = static::find($this->attributes[static::$primaryKey]);
        $this->attributes = $fresh->attributes;
        $this->original = $fresh->original;
        
        return $this;
    }
}