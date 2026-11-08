<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

final class Foo_Controller
{
    /** @var array<string, string>|null */
    public static ?array $lastParams = null;

    /**
     * @param array<string, string> $params
     */
    public function barAction(array $params): void
    {
        self::$lastParams = $params;
    }
}
