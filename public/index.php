<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 09:55
 */

//error_reporting(0);
use App\System\App;
use App\System\Exceptions\ControllerException;
use App\System\Router;

header("Content-Type:text/html;charset=utf-8");

require "../vendor/autoload.php";
require "../config/app.php";

session_start();


try{
    $obj = new App();
    $obj->run();
}
catch(ControllerException $e) {
    echo $e->getMessage();
    return;
}