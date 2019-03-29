<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 10:43
 */

namespace App\Controller;


use App\Model\User;
use App\System\Controller\AbstractController;

class Index_Controller extends AbstractController
{
    public function indexAction() {

        /*$testObj = new User();
        $testObj->name = "asdasd";
        $testObj->email = "sdsadsadasdas";
        $testObj->pass = 1;
        $testObj->save();*/

        $this->page = $this->view->render('index/index',['title' => "Hello world"]);
        return $this->output();

    }
}