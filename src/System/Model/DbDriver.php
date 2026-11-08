<?php

declare(strict_types=1);

namespace App\System\Model;

/**
 * Thin PDO wrapper. Always talks to the database through prepared
 * statements with bound parameters — never through string-built SQL.
 */
final class DbDriver implements DbDriverInterface
{
    private \PDO $pdo;

    public function __construct(
        string $host,
        int $port,
        string $database,
        string $user,
        string $pass,
    ) {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $database);

        $this->pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    /**
     * Wrap an already-configured PDO connection (e.g. an in-memory SQLite
     * connection in tests) instead of opening a new MySQL connection.
     */
    public static function fromPdo(\PDO $pdo): self
    {
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $driver = (new \ReflectionClass(self::class))->newInstanceWithoutConstructor();
        $driver->pdo = $pdo;

        return $driver;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
