<?php

declare(strict_types=1);

use App\System\Model\DbDriverInterface;
use App\System\Model\Model;
use DI\ContainerBuilder;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->addDefinitions(
    require __DIR__ . '/../config/app.php',
    require __DIR__ . '/../config/database.php',
    require __DIR__ . '/../config/dependencies.php',
);

$container = $builder->build();

// Lazy: the connection is only opened once a Model actually runs a query.
Model::setConnection(static fn () => $container->get(DbDriverInterface::class));

return $container;
