<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Meits
 * Date: 28-Mar-19
 * Time: 11:29
 */

namespace App\System\View;


class View implements IView
{

    private $basePath;
    private $templatePath;

    /**
     * View constructor.
     * @param $basePath
     * @param $templatePath
     */
    public function __construct(string $basePath, string $templatePath)
    {
        $this->basePath = $basePath;
        $this->templatePath = $templatePath;
    }


    public function render(string $path, array $param = array()) : string
    {

        extract($param);
        ob_start();

        if (!include($this->basePath.'/'.$this->templatePath . '/' . $path . '.php')) {
            throw new \Exception('View Error');
        }

        return ob_get_clean();
    }
}