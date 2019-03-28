<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 15:35
 */

namespace App\System;


use App\System\Model\DbDriver;

class App
{

    private $router;
    private $dbDriver;
    /**
     *
     */
    public function run() {

        $this->dbDriver = DbDriver::get_instance();
        $this->dbDriver->setConnection(HOST, USER, PASSWORD, DB_NAME);


        $this->router = new Router();
        $this->router->processRequest();
    }
}