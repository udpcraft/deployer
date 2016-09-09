<?php

namespace Afanty\Helper;


class Singleton
{
    private static $_instances = array();

    protected function __construct()
    {
    }

    final public function __clone()
    {
        trigger_error("clone method is not allowed.", E_USER_ERROR);
    }

    /**
     * @return static
     */
    final public static function getInstance($type = '')
    {
        $c = get_called_class();
        $k = $c . $type;
        
        if(! isset(self::$_instances[$k])) {
            self::$_instances[$k] = new $c($type);
        }
        
        return self::$_instances[$k];
    }
}
