<?php
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 09:56
 */
declare(strict_types=1);

namespace App\System;


use App\System\Exceptions\ControllerException;

class Router
{
    private $request_url;

    private $controller;

    private $action;

    private $params;

    static $_instance;

    static function get_instance() {

        if(self::$_instance instanceof self) {
            return self::$_instance;
        }

        return self::$_instance = new self;
    }

    public function __construct() {

        $zapros = $_SERVER['REQUEST_URI'];

        $path = substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'index.php'));

        if($path === SITE_URL) {
            $this->request_url = substr($zapros,strlen(SITE_URL));

            $url = explode('/',rtrim($this->request_url,'/'));

            $this->controller .= "App\\Controller\\";
            if (!empty($url[0])) {
                $this->controller .= ucfirst($url[0]).'_Controller';
            }
            else {
                $this->controller .= "Index_Controller";
            }

            if (!empty($url[1])) {
                $this->action .= ucfirst($url[1]).'Action';
            }
            else {
                $this->action .= "indexAction";
            }

            if(!empty($url[2])) {

                $count = count($url);

                $key = array();
                $value = array();

                for($i = 2;$i < $count; $i++) {

                    if($i%2 == 0) {
                        $key[] = $url[$i];
                    }
                    else {
                        $value[] = $url[$i];
                    }
                }

                if(!$this->params = array_combine($key,$value)) {
                    throw new ContrException("Не правильный адресс",$zapros);
                }
            }
        }
        else {
            try{
                throw new \Exception('<p style="color:red">Не правильный адресс сайта.</p>');
            }
            catch(Exception $e) {
                echo $e->getMessage();
                exit();
            }
        }

    }

    public function processRequest()
    {
        if (class_exists($this->controller)) {

            $ref = new \ReflectionClass($this->controller);

            if ($ref->hasMethod($this->action)) {

                if ($ref->isInstantiable()) {
                    $class = $ref->newInstance();
                    $method = $ref->getMethod($this->action);
                    $method->invoke($class, $this->get_params());
                }
            }

        } else {
            throw new ControllerException('Page Error');
        }
    }



    public function get_controller()
    {
        return $this->controller;
    }

    public function get_params()
    {
        return $this->params;
    }
}