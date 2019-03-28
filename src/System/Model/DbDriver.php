<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 14:39
 */

namespace App\System\Model;


class DbDriver
{

    private static $_instance;
    private $ins_db = null;

    public static function get_instance() {

        if(self::$_instance instanceof static) {
            return self::$_instance;
        }

        return self::$_instance = new static();
    }

    private function __construct()
    {
    }

    /**
     * DbDriver constructor.
     */
    public function setConnection($host, $user, $pass, $dbname)
    {
        try {
            $this->ins_db = new \mysqli($host, $user, $pass, $dbname);

            if($this->ins_db->connect_error) {
                throw new DbException("Ошибка соединения : ".$this->ins_db->connect_errno."|".iconv("CP1251","UTF-8",$this->ins_db->connect_error));
            }

            $this->ins_db->query("SET NAMES 'UTF8'");
        }
        catch(DbException $e) {
            exit();
        }
    }

    /**
     * @return null
     */
    public function getInsDb()
    {
        return $this->ins_db;
    }
}