<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 11:29
 */

namespace App\System\Controller;


use App\System\View\View;

class AbstractController
{
    protected $view;
    protected $page;

    /**
     * AbstractController constructor.
     * @param $view
     */
    public function __construct()
    {
        $this->view = new View(BASEPATH, TEMPLATE);
    }

    public function get_page()
    {
        echo $this->page;
    }

    protected function output()
    {
        return $this->get_page();
    }

}