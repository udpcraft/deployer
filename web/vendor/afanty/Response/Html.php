<?php

namespace Afanty\Response;

use Afanty\Routing\Router;
use Afanty\Config\Env;


class Html implements IResponse
{
    private static $_viewExt = '.html';

    public function write($result)
    {
        $router = Router::getInstance();
        
        $viewFile = $this->_getViewFile();

        $result['_param']['viewFile'] = $viewFile;
        $result['_param']['indexAction'] = '/' . trim(join('/', array($router->getModule(), $router->getController())));

        return self::_render(self::_getLayout(), $result);
    }

    public static function renderPartial($result = [])
    {
        return self::_render(self::_getViewFile(), $result);
    }

    private static function _getViewFile()
    {
        $router = Router::getInstance();
        $rootDir = Env::getRootDir();

        $viewFile = $rootDir . '/app/Modules/' .
                  trim(join('/', array(ucfirst($router->getModule()),
                                       'View',
                                       strtolower($router->getController()),
                                       $router->getView())),'/') .
                  self::$_viewExt;

        if (! file_exists($viewFile)) {
            throw new \Afanty\Exception\Response('bad view, missing ' . $viewFile);
        }

        return $viewFile;
    }

    private static function _render($viewFile, $result)
    {
        header("Content-type: text/html; charset=utf-8");

        extract($result);

        include($viewFile);

        exit;
    }

    private static function _getLayout()
    {
        return Env::getRootDir() . '/app/Modules/Site/View/layouts/main.html';
    }
}