<?php

namespace Afanty\Web\Response;

use Afanty\Helper\Factory;


class Http
{
    public static function render($result, $type)
    {
        $render = Factory::getResponseRender($type);

        $render->write($result);
    }

	public static function redirect($url, $code = 302)
    {
        header("Location:$url", true, $code);
        exit();
    }

    public static function cookie($name, $value = null, $expire = null, $path = '/', $domain = null, $secure = false, $httpOnly = false)
    {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

}