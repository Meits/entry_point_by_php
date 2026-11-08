<?php

declare(strict_types=1);

namespace App\System\Model;

interface DbDriverInterface
{
    /**
     * Run a prepared statement and return it, already executed.
     *
     * @param list<scalar|null> $params positional values bound to the `?` placeholders in $sql
     */
    public function query(string $sql, array $params = []): \PDOStatement;

    public function lastInsertId(): string;
}
