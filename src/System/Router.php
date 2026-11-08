<?php

declare(strict_types=1);

namespace App\System;

use App\System\Exceptions\RouteNotFoundException;
use Psr\Container\ContainerInterface;

/**
 * Maps a request URL onto a `{Segment}_Controller::{segment}Action()` call,
 * e.g. `/blog/show/id/42` -> `BlogController::showAction(['id' => '42'])`.
 *
 * Controllers are resolved through the DI container (not raw
 * `new $class()`) so their own constructor dependencies get autowired.
 */
final class Router
{
    private const SEGMENT_PATTERN = '/^[A-Za-z][A-Za-z0-9]*$/';

    private readonly string $controllerClass;
    private readonly string $actionMethod;

    /** @var array<string, string> */
    private readonly array $params;

    public function __construct(
        private readonly ContainerInterface $container,
        string $siteUrl = '/',
        private readonly string $controllerNamespace = 'App\\Controller\\',
        ?string $requestUri = null,
        ?string $scriptName = null,
    ) {
        $requestUri ??= $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName ??= $_SERVER['PHP_SELF'] ?? '/index.php';

        $basePath = substr($scriptName, 0, (int) strpos($scriptName, 'index.php'));

        if ($basePath !== $siteUrl) {
            throw new RouteNotFoundException(sprintf('Unexpected site base path "%s", expected "%s".', $basePath, $siteUrl));
        }

        $path = strtok(substr($requestUri, strlen($siteUrl)), '?') ?: '';
        $segments = array_values(array_filter(explode('/', rtrim($path, '/')), static fn (string $s) => $s !== ''));

        $this->controllerClass = $this->resolveControllerClass($segments[0] ?? null);
        $this->actionMethod = $this->resolveActionMethod($segments[1] ?? null);
        $this->params = $this->pairParams(array_slice($segments, 2));
    }

    public function processRequest(): void
    {
        if (!class_exists($this->controllerClass) || !method_exists($this->controllerClass, $this->actionMethod)) {
            throw new RouteNotFoundException(sprintf('No route matches %s::%s().', $this->controllerClass, $this->actionMethod));
        }

        $controller = $this->container->get($this->controllerClass);
        $controller->{$this->actionMethod}($this->params);
    }

    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    public function getActionMethod(): string
    {
        return $this->actionMethod;
    }

    /** @return array<string, string> */
    public function getParams(): array
    {
        return $this->params;
    }

    private function resolveControllerClass(?string $segment): string
    {
        $name = $this->validateSegment($segment, 'index');

        return sprintf('%s%s_Controller', $this->controllerNamespace, ucfirst($name));
    }

    private function resolveActionMethod(?string $segment): string
    {
        return lcfirst($this->validateSegment($segment, 'index')) . 'Action';
    }

    private function validateSegment(?string $segment, string $default): string
    {
        $name = $segment !== null && $segment !== '' ? $segment : $default;

        if (!preg_match(self::SEGMENT_PATTERN, $name)) {
            throw new RouteNotFoundException(sprintf('Invalid URL segment "%s".', $segment ?? ''));
        }

        return $name;
    }

    /**
     * @param list<string> $segments
     * @return array<string, string>
     */
    private function pairParams(array $segments): array
    {
        if (count($segments) % 2 !== 0) {
            throw new RouteNotFoundException('URL parameters must come in key/value pairs.');
        }

        $params = [];

        foreach (array_chunk($segments, 2) as [$key, $value]) {
            $params[$key] = $value;
        }

        return $params;
    }
}
