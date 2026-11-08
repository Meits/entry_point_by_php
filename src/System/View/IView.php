<?php

declare(strict_types=1);

namespace App\System\View;

interface IView
{
    /**
     * @param array<string, mixed> $param variables exposed to the template
     */
    public function render(string $path, array $param = []): string;
}
