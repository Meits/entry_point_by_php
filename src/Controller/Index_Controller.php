<?php

declare(strict_types=1);

namespace App\Controller;

use App\System\Controller\AbstractController;

class Index_Controller extends AbstractController
{
    /**
     * @param array<string, string> $params
     */
    public function indexAction(array $params = []): void
    {
        $this->page = $this->view->render('index/index', ['title' => 'Hello world']);
        $this->output();
    }
}
