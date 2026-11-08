# entry_point_by_php

A hand-rolled, from-scratch HTTP entry point: no framework, just a router, an
ActiveRecord-style model and a plain-PHP view layer, wired together with
[PHP-DI](https://php-di.org/).

## Requirements

- PHP 8.4+
- ext-pdo (plus the PDO driver for whatever database you use, e.g. `pdo_mysql`)
- Composer

## Setup

```bash
composer install
cp .env.example .env
```

Edit `.env` with your database credentials if you plan to use the `Model`
layer (the database connection is opened lazily — the demo home page works
without any database configured).

## Running

```bash
composer serve
```

Then open http://localhost:8000.

## Tests

```bash
composer test
```

## How it works

- `public/index.php` builds the DI container (`bootstrap/container.php`) and
  runs `App::run()`.
- `App` just delegates to `Router`, which maps a URL to a controller action:
  `/blog/show/id/42` → `App\Controller\Blog_Controller::showAction(['id' => '42'])`.
  The controller itself is resolved through the container, so its
  constructor dependencies (e.g. the view) are autowired.
- Controllers extend `App\System\Controller\AbstractController`, which gets
  a `View` injected and exposes `output()` to print the rendered page.
- `App\System\Model\Model` is a minimal ActiveRecord base class
  (`select()/where()/get()/save()/destroy()`, plus `Model::all()` /
  `Model::find($id)`). All queries go through bound parameters via
  `DbDriverInterface::query($sql, $params)` — no SQL is ever built by
  concatenating values into a string.
- Configuration (`config/app.php`, `config/database.php`) is plain PHP
  returning arrays read from the environment (`.env`, loaded via
  `vlucas/phpdotenv`) — there are no global constants.

## Frontend assets

`resources/js`, `resources/sass`, `public/js`, `public/css` and the
Laravel-Mix/webpack toolchain are a separate, pre-existing front-end build
and are untouched by the PHP refactor.
