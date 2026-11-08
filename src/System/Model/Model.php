<?php

declare(strict_types=1);

namespace App\System\Model;

/**
 * Minimal ActiveRecord-style base model: a tiny fluent query builder plus
 * save()/destroy() for the current row, backed by a single shared
 * {@see DbDriverInterface} connection (set once via {@see self::setConnection()}).
 *
 * All generated SQL uses `?` placeholders — user-supplied values are never
 * concatenated into the query string.
 */
class Model
{
    private static \Closure|DbDriverInterface|null $connection = null;

    protected string $table = '';

    /** @var list<string> */
    private array $select = ['*'];
    private string $from = '';
    /** @var list<string> */
    private array $whereSql = [];
    /** @var list<scalar|null> */
    private array $whereParams = [];
    private ?string $limit = null;

    /** @var array<string, scalar|null> */
    private array $data = [];
    private int|string|null $id;

    /**
     * Accepts either a ready connection or a closure that lazily builds one
     * on first use, so a request that never touches the database never
     * opens one.
     */
    public static function setConnection(\Closure|DbDriverInterface $connection): void
    {
        self::$connection = $connection;
    }

    protected static function connection(): DbDriverInterface
    {
        if (self::$connection instanceof \Closure) {
            self::$connection = (self::$connection)();
        }

        return self::$connection
            ?? throw new \LogicException(static::class . '::setConnection() must be called before using any model.');
    }

    public function __construct(int|string|null $id = null)
    {
        if ($this->table === '') {
            $this->table = strtolower((new \ReflectionClass($this))->getShortName()) . 's';
        }
        $this->from = $this->table;
        $this->id = $id;

        if ($id !== null) {
            $row = (new static())->where('id', $id)->first();

            if ($row === null) {
                throw new \RuntimeException(sprintf('%s with id "%s" not found.', static::class, $id));
            }

            $this->data = $row;
        }
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        return match ($name) {
            'all' => (new static())->get(),
            'find' => (new static())->where('id', $arguments[0])->first(),
            default => throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $name)),
        };
    }

    public function select(string ...$columns): static
    {
        $this->select = $columns === [] ? ['*'] : $columns;

        return $this;
    }

    public function from(?string $table = null): static
    {
        $this->from = $table ?? $this->table;

        return $this;
    }

    public function where(string $field, int|string|float|bool|null $operator, int|string|float|bool|null $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->whereSql[] = sprintf('%s %s ?', $field, $operator);
        $this->whereParams[] = $value;

        return $this;
    }

    public function limit(int $offset, int $count): static
    {
        $this->limit = sprintf('%d, %d', $offset, $count);

        return $this;
    }

    /**
     * @return list<array<string, scalar|null>>
     */
    public function get(): array
    {
        $sql = $this->selectPrepare() . $this->fromPrepare() . $this->wherePrepare() . $this->limitPrepare();

        return static::connection()->query($sql, $this->whereParams)->fetchAll();
    }

    /**
     * @return array<string, scalar|null>|null
     */
    public function first(): ?array
    {
        $rows = $this->limit(0, 1)->get();

        return $rows[0] ?? null;
    }

    public function save(): bool|string
    {
        return $this->id !== null ? $this->update() : $this->insert();
    }

    public function destroy(): bool
    {
        if ($this->id === null) {
            throw new \LogicException('Cannot destroy a record that has no id.');
        }

        static::connection()->query(
            sprintf('DELETE FROM %s WHERE id = ?', $this->table),
            [$this->id],
        );

        return true;
    }

    private function insert(): string
    {
        if ($this->data === []) {
            throw new \LogicException('Cannot insert a record with no data set.');
        }

        $columns = array_keys($this->data);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table, implode(', ', $columns), $placeholders);

        static::connection()->query($sql, array_values($this->data));

        return static::connection()->lastInsertId();
    }

    private function update(): bool
    {
        $assignments = implode(', ', array_map(static fn (string $column) => "$column = ?", array_keys($this->data)));
        $params = [...array_values($this->data), $this->id];

        $sql = sprintf('UPDATE %s SET %s WHERE id = ?', $this->table, $assignments);

        static::connection()->query($sql, $params);

        return true;
    }

    private function selectPrepare(): string
    {
        return 'SELECT ' . implode(', ', $this->select);
    }

    private function fromPrepare(): string
    {
        return ' FROM ' . $this->from;
    }

    private function wherePrepare(): string
    {
        return $this->whereSql === [] ? '' : ' WHERE ' . implode(' AND ', $this->whereSql);
    }

    private function limitPrepare(): string
    {
        return $this->limit === null ? '' : ' LIMIT ' . $this->limit;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }
}
