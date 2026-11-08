<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\System\Model\Model;

final class Widget extends Model
{
    protected string $table = 'widgets';
}
