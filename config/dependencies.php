<?php

declare(strict_types=1);

use App\System\Model\DbDriver;
use App\System\Model\DbDriverInterface;
use App\System\Router;
use App\System\View\IView;
use App\System\View\View;
use Psr\Container\ContainerInterface;

return [
    DbDriverInterface::class => DI\factory(static function (ContainerInterface $c): DbDriverInterface {
        $db = $c->get('database');

        return new DbDriver($db['host'], $db['port'], $db['name'], $db['user'], $db['pass']);
    }),

    IView::class => DI\factory(static function (ContainerInterface $c): IView {
        $app = $c->get('app');

        return new View($app['base_path'], $app['template_path']);
    }),

    Router::class => DI\factory(static function (ContainerInterface $c): Router {
        $app = $c->get('app');

        return new Router($c, $app['site_url'], controllerNamespace: 'App\\Controller\\');
    }),
];
