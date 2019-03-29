<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 09:55
 */

//error_reporting(0);
use App\System\App;

require "../vendor/autoload.php";
require "../config/app.php";

try{
    $obj = new App();
    $obj->run();
}
catch(\Exception $e) {
    echo $e->getMessage();
    return;
}