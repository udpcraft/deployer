<?php

namespace Afanty\Routing;

use Afanty\Web\Request\Http as Request;
use Afanty\Web\Response\Http as Response;
use Afanty\Helper\Singleton;


class Router extends Singleton
{
    private $_module;
    private $_controller;
    private $_action;
    private $_view = null;

    public function create()
    {
        $resource = Request::getRequestResouce();

        Request::validateResource($resource);

        $this->_module = $resource['_module'];
        $this->_controller = $resource['_controller'];
        $this->_action = $this->_parseAction($resource['_action']);

        return $this;
    }

    public function dispatch()
    {
        $controllerInstance = $this->_getCalledController();

        $action = $this->_getCalledAction($this->_action);

        $result = $controllerInstance->run($action);

        $resourceType = Request::getResourceType();

        foreach ($controllerInstance->getDecorator($resourceType) as $decorator) {
            $result = $decorator::decorate($result);
        }

        Response::render($result, $resourceType);
    }

    private function _parseAction($action)
    {
        $actions = explode('.', $action);

        $resource = [];

        if (! empty($actions[1])) {
            Request::setResourceType($actions[1]);
        }

        return $actions[0];
    }


    private function _getCalledController()
    {
        $controllerClass = '\Modules\\' . ucfirst($this->_module) . '\Controller\\' . ucfirst($this->_controller);

        if (! class_exists($controllerClass)) {
            throw new \Afanty\Exception\Request("class not fount [$controllerClass]");
        }

        $controllerInstance = new $controllerClass;

        return $controllerInstance;
    }

    private function _getCalledAction($action)
    {
        if (strpos($action, '-') !== false) {
            $action = explode('-', $action);

            $func = function($item) {
                return ucfirst($item);
            };

            $action = implode('', array_map($func, $action));
        }

        return lcfirst($action) . 'Action';
    }

    public function getModule()
    {
        return $this->_module;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function getAction()
    {
         return $this->_action;
    }

    public function getView()
    {
        if ($this->_view) {
            return $this->_view;
        }

        return $this->_action;
    }
}