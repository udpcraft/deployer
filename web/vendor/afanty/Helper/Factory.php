<?php

namespace Afanty\Helper;


class Factory
{
    public static function getResponseRender($type)
    {
        $renderClass = '\Afanty\\Response\\' . ucfirst($type);

        if (class_exists($renderClass)) {
            return new $renderClass;
        }

        throw new \Afanty\Exception\Response("unsuppot Response render [$type]");
    }

    public static function getMemcache($type = 'spam')
    {
        return \Afanty\Cache\Memcache::getInstance($type)->getHandle();
    }

    public static function getMysql($dbType)
    {
        return \Afanty\Db\Mysql::getInstance($dbType)->getHandle();
    }

    public static function getSqlite()
    {
        return \Afanty\Db\Sqlite::getInstance()->getHandle();
    }
}
