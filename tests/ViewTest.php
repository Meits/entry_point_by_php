<?php

declare(strict_types=1);

namespace App\Tests;

use App\System\View\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    public function testRendersTemplateWithVariables(): void
    {
        $view = new View(__DIR__, 'Fixtures/views');

        $html = $view->render('greeting', ['name' => 'World']);

        self::assertStringContainsString('Hello, World!', $html);
    }

    public function testThrowsWhenTemplateIsMissing(): void
    {
        $view = new View(__DIR__, 'Fixtures/views');

        $this->expectException(\RuntimeException::class);

        $view->render('does-not-exist');
    }
}
