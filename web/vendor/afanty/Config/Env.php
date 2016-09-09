<?php

namespace Afanty\Config;


class Env
{
    private static $_configs = null;

    private static $_rootDir = null;

    public static function init($rootDir)
    {
        if (! self::$_rootDir) {
            self::$_rootDir = $rootDir;
        }
    }

    public static function load($configFile)
    {
        return self::_parseIni($configFile);
    }

    private static function _parseIni($iniFile)
    {
        if (! self::$_configs) {
            self::$_configs = parse_ini_file($iniFile, true, INI_SCANNER_TYPED);
        }

        return self::$_configs;
    }

    public static function getConfig($section, $item = '')
    {
        if ($item === '' && isset(self::$_configs[$section])) {
            return self::$_configs[$section];
        }

        if (empty(self::$_configs[$section][$item])) {
            throw new \Afanty\Exception\Request("unknow config [$section] [$item]");
        }

        return self::$_configs[$section][$item];
    }

    public static function getRootDir()
    {
        return self::$_rootDir;
    }

}