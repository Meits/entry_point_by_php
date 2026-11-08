<?php

declare(strict_types=1);

namespace App\Tests;

use App\System\Exceptions\RouteNotFoundException;
use App\System\Router;
use App\Tests\Fixtures\FakeContainer;
use App\Tests\Fixtures\Foo_Controller;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private function makeRouter(string $requestUri, string $scriptName = '/index.php'): Router
    {
        return new Router(
            container: new FakeContainer(),
            siteUrl: '/',
            controllerNamespace: 'App\\Tests\\Fixtures\\',
            requestUri: $requestUri,
            scriptName: $scriptName,
        );
    }

    public function testParsesControllerActionAndParams(): void
    {
        $router = $this->makeRouter('/foo/bar/id/42');

        self::assertSame('App\\Tests\\Fixtures\\Foo_Controller', $router->getControllerClass());
        self::assertSame('barAction', $router->getActionMethod());
        self::assertSame(['id' => '42'], $router->getParams());
    }

    public function testStripsQueryString(): void
    {
        $router = $this->makeRouter('/foo/bar?utm_source=test');

        self::assertSame('barAction', $router->getActionMethod());
        self::assertSame([], $router->getParams());
    }

    public function testDefaultsToIndexForEmptyPath(): void
    {
        $router = $this->makeRouter('/');

        self::assertSame('App\\Tests\\Fixtures\\Index_Controller', $router->getControllerClass());
        self::assertSame('indexAction', $router->getActionMethod());
    }

    public function testRejectsOddNumberOfParamSegments(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->makeRouter('/foo/bar/id');
    }

    public function testRejectsInvalidSegmentCharacters(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->makeRouter('/foo-bar/baz');
    }

    public function testRejectsUnexpectedBasePath(): void
    {
        $this->expectException(RouteNotFoundException::class);

        $this->makeRouter('/foo/bar', '/sub/index.php');
    }

    public function testProcessRequestInvokesResolvedAction(): void
    {
        Foo_Controller::$lastParams = null;

        $router = $this->makeRouter('/foo/bar/id/42');
        $router->processRequest();

        self::assertSame(['id' => '42'], Foo_Controller::$lastParams);
    }
}
