<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 13:56
 */

namespace App\System\View;


interface IView
{
    public function render(string $path, array $param = array()) : string;
}