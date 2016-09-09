<?php

namespace Event\Request;
use Afanty\Routing\Router;

class Auth implements \Afanty\Events\Event
{
    public function raise()
    {
        $userInfo = \Afanty\Web\Request\Http::getSession('user_info');

        if (empty($userInfo['uid'])) {
            $authorizeResource = [];
            $roleIds = [];
        } else {
            $roleIds = \Modules\User\Model\UserRoles::getInstance()->getRoleIdsByUserId($userInfo['uid']);
            $authorizeResource = $this->_getAuthorizedResource($userInfo['uid'], $roleIds);
        }

        $authorizeResource = array_merge($authorizeResource, \Components\Permission::whiteList());

        \Afanty\Web\Request\Http::putSession('user_auth', $authorizeResource);
        \Afanty\Web\Request\Http::putSession('user_roles', $roleIds);

        if (! in_array($this->_getCurrentResource(), $authorizeResource)) {
            throw new \Afanty\Exception\Forbidden('未授权的action [' . $this->_getCurrentResource() . ']');
        }
    }

    private function _getAuthorizedResource($userId, $roleIds)
    {
        $resources = \Modules\User\Model\Roles::getInstance()->getInfoByIds($roleIds);

        $authorizeResource = [];
        foreach ($resources as $resource) {
            $r = json_decode(base64_decode($resource['permission']), true);
            $authorizeResource = array_merge($authorizeResource, $r);
        }

        return array_unique($authorizeResource);
    }

    private function _getCurrentResource()
    {
        $router = Router::getInstance();

        $resource['module']     = $router->getModule();
        $resource['controller'] = $router->getController();
        $resource['action']     = $router->getAction();

        return '/' . implode('/', $resource);
    }
}