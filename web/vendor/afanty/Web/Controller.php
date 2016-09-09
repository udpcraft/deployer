<?php

namespace Afanty\Web;

use \Afanty\Web\Request\Http as Request;
use \Afanty\Web\Response\Http as Response;

abstract class Controller
{
    protected $_decorators = [];

    public function run($actionName)
    {
        if (method_exists($this, $actionName)) {
            return call_user_func(array($this, $actionName));
        }

        throw new \Afanty\Exception\Request("bad action [$actionName]");
    }

    public function get($key,$default='')
    {
        return Request::get($key,$default);
    }

    protected function post($key,$default='')
    {
        return Request::post($key,$default);
    }

    protected function getParam($key)
    {
        return Request::getParam($key);
    }

    protected function redirect($url,$code=302)
    {
        Response::redirect($url,$code);
    }

    protected function _setDecorator($renderType, $decorator)
    {
        $this->_decorators[$renderType][] = $decorator;
    }

    public function getDecorator($renderType)
    {
        if (empty($this->_decorators[$renderType])) {
            return [];
        }

        return $this->_decorators[$renderType];
    }
}
