<?php

declare(strict_types=1);

namespace App\System\Exceptions;

/**
 * Thrown when the requested URL cannot be resolved to a controller action.
 */
class RouteNotFoundException extends ControllerException
{
}
