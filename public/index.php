<?php

declare(strict_types=1);

use App\System\App;
use App\System\Exceptions\RouteNotFoundException;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../bootstrap/container.php';

try {
    $container->get(App::class)->run();
} catch (RouteNotFoundException $e) {
    http_response_code(404);
    echo $container->get('app')['debug'] ? $e->getMessage() : 'Page not found.';
} catch (\Throwable $e) {
    http_response_code(500);

    if ($container->get('app')['debug']) {
        echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        error_log((string) $e);
        echo 'Something went wrong.';
    }
}
