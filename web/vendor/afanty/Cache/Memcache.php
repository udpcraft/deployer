<?php

namespace Afanty\Cache;


class Memcache extends \Afanty\Helper\Singleton
{
    private $_memcache = null;

    private $_type = null;

    protected function __construct($type)
    {
        $this->_type = $type;
        $this->_memcache = $this->_connect();
    }

    public function getHandle()
    {
        return $this->_memcache;
    }

    private function _connect()
    {
        $config = \Afanty\Config\Env::getConfig('memcache');

        if (empty($config[$this->_type])) {
            throw new \Afanty\Exception\Response("Can't connect memcache ( {$this->_type} ).");
        }

        $config = $config[$this->_type];

        $cache = new \Memcached();
        $cache->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
        $cache->setOption(\Memcached::OPT_COMPRESSION, false);
        $cache->addServer($config['host'], $config['port']);

        if (! empty($config['user']) && ! empty($config['pass'])) {
            $cache->setSaslAuthData($config['user'], $config['pass']);
        }

        return $cache;
    }
}