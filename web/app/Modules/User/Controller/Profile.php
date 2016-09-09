<?php

namespace Modules\User\Controller;

class Profile extends \Afanty\Web\Controller
{
    public function loginAction()
    {
        // check is logedin
        // check if do login
        // render login template

        if (! \Afanty\Web\Request\Http::isPost()) {
            \Afanty\Response\Html::renderPartial();
        }

        $username = $this->post('username');
        $password = $this->post('password');

        $userInfo = \Modules\User\Model\Users::getInstance()->getInfoByName($username);

        if (md5(md5($password)) == $userInfo['password']) {

            $rsaKey = \Helper\Nonce::getSalt(8);
            $zKey = \Helper\Crypt\Rsa::encrypt($rsaKey);
            $userStr = sprintf("%s|%s", $userInfo['id'], $userInfo['name']);
            $zTicket = \Helper\Crypt\Des::encrypt($userStr,$rsaKey);

            \Afanty\Web\Response\Http::cookie('zs', $zKey, 0, '/');
            \Afanty\Web\Response\Http::cookie('zc', $zTicket, 0, '/');

            $this->redirect('/');
        } else {
            \Afanty\Response\Html::renderPartial(['is_login' => false]);
        }
    }

    public function logoutAction()
    {
        \Afanty\Web\Response\Http::cookie('zs', '', 0, '/');
        \Afanty\Web\Response\Http::cookie('zc', '', 0, '/');
        session_unset();

        $this->redirect('/user/profile/login');
    }

}