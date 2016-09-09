<?php

namespace Afanty\Web\Request;

use Afanty\Utils\Validators;


class Http
{

    private static $_defaultResouce = array(
        '_module'     => 'site',
        '_controller' => 'home',
        '_action'     => 'index',
    );

    private static $_resouceType = 'html';

    public static function get($key, $default = '')
    {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public static function post($key, $default = '')
    {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    public static function getParam($key)
    {
        $value = self::get($key);

        if (!empty($value)){
            return self::get($key);
        }

        return self::post($key);
    }

    public static function getCookie($key = null, $default = '')
    {
        if (null === $key) {
            return $_COOKIE;
        }

        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }


    public static function putSession($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    public static function getSession($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public static function getRequestResouce()
    {
        //合并默认的请求参数
        foreach (self::$_defaultResouce as $field => $value) {
            if (!empty($_REQUEST[$field])) {
                $resource[$field] = $_REQUEST[$field];
            } else {
                $resource[$field] = $value;
            }
        }

        unset($_REQUEST['_module']);
        unset($_REQUEST['_controller']);
        unset($_REQUEST['_action']);

        return $resource;
    }

    public static function validateResource(Array $resource)
    {
        foreach (array_keys(self::$_defaultResouce) as $field) {
            if (empty($resource[$field])) {
                throw new \Afanty\Exception\Request("request param [$field] is required");
            }

            if (! Validators::isUriPath($resource[$field])) {

                throw new \Afanty\Exception\Request("bad param [{$field}]");
            }
        }
    }

    public static function setResourceType($type)
    {
        self::$_resouceType = $type;
    }

    public static function getResourceType()
    {
        return self::$_resouceType;
    }

    public static function header($header)
    {
        if (empty($header)) {
            return null;
        }

        // Try to get it from the $_SERVER array first
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (!empty($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }

        return false;
    }

    public static function isAjax()
    {
        return ('XMLHttpRequest' == self::header('X_REQUESTED_WITH'));
    }

    public static function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] === 'POST');
    }

    public static function isFlashRequest()
    {
        return ('Shockwave Flash' == self::header('USER_AGENT'));
    }

}
