<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class FakeContainer implements ContainerInterface
{
    /** @param array<string, mixed> $items */
    public function __construct(private readonly array $items = [])
    {
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            if (class_exists($id)) {
                return new $id();
            }

            throw new class ("No entry for \"$id\"") extends \RuntimeException implements NotFoundExceptionInterface, ContainerExceptionInterface {
            };
        }

        return $this->items[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->items);
    }
}
