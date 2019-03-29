<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 29-Mar-19
 * Time: 13:06
 */

namespace App\System\Model;


interface IDbDriver
{
    public function setConnection($host, $user, $pass, $dbname);
    public function query($sql);
    public function getInsDb();
}