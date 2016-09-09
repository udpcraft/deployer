<?php

namespace Event\Request;
use Afanty\Routing\Router;


class Login implements \Afanty\Events\Event
{

    public function raise()
    {
        if (! $this->_isLogin()) {
            //redirect to login page
            $loginUrl = '/'. implode("/", array_values($this->_whiteList()));
            \Afanty\Web\Response\Http::redirect($loginUrl);
        }
    }

    private function _isLogin()
    {
        $router = Router::getInstance();

        $resource['module']     = $router->getModule();
        $resource['controller'] = $router->getController();
        $resource['action']     = $router->getAction();


        if (! array_diff($resource, $this->_whiteList())) {
            return true;
        }

        $zKey    = \Afanty\Web\Request\Http::getCookie('zs');
        $zTicket = \Afanty\Web\Request\Http::getCookie('zc');

        if (! $zKey || ! $zTicket) {
            return false;
        }

        $zKey = \Helper\Crypt\Rsa::decrypt($zKey);
        $userStr = \Helper\Crypt\Des::decrypt($zTicket,$zKey);

        if (! $userStr) {
            return false;
        }

        $userArr = explode('|',$userStr);

        $userInfo = ['uid' => $userArr[0], 'name' => $userArr[1]];

        \Afanty\Web\Request\Http::putSession('user_info', $userInfo);

        return $userInfo;
    }

    private function _whiteList()
    {
        return [
            'module' => 'user',
            'controller' => 'profile',
            'action' => 'login',
        ];
    }
}