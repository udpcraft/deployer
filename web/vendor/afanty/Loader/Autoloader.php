<?php

namespace Afanty\Loader;


class Autoloader
{
    protected static $_namespaces = [];

    public static function addNamespace($namespace, $dir)
    {
        self::$_namespaces[$namespace][] = realpath($dir);
    }

    public static function register()
    {
        spl_autoload_register(array(__CLASS__, '_loadClass'));
    }

    public static function unregister()
    {
        spl_autoload_unregister(array(__CLASS__, '_loadClass'));
    }

    protected static function _loadClass($class)
    {
        $loadedClassMapping = self::_getLoadedClassMapping($class);

        foreach ($loadedClassMapping['dirs'] as $dir) {
            $file = $dir . DIRECTORY_SEPARATOR . $loadedClassMapping['path'];

            if (is_readable($file) && (include $file)) {
                return;
            }            
        }
    }

    private static function _getLoadedClassMapping($class)
    {
        $mapping['dirs'] = [];
        
        foreach (self::$_namespaces as $prefix => $dirs) {
            $position = strlen($prefix);
            if (strncmp($prefix, $class, $position) !== 0) {
                continue;
            }

            $path = substr($class, $position + 1);
            $path = str_replace('\\', DIRECTORY_SEPARATOR, $path) . '.php';

            $mapping['path'] = $path;
            $mapping['dirs'] = $dirs;

            return $mapping;
        }

        if (isset(self::$_namespaces[APP_LOADER_PREFIX])) {
            $mapping['dirs'] = self::$_namespaces[APP_LOADER_PREFIX];

            $mapping['path'] = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        }
        
        return $mapping;
        
    }
}
