<?php

declare(strict_types=1);

namespace App\System\Controller;

use App\System\View\IView;

abstract class AbstractController
{
    protected string $page = '';

    public function __construct(protected readonly IView $view)
    {
    }

    protected function output(): void
    {
        echo $this->page;
    }
}
