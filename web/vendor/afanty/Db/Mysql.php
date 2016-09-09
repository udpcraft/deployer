<?php

namespace Afanty\Db;


class Mysql extends \Afanty\Helper\Singleton
{
    private $_mysql = null;

    private $_type = null;

    protected function __construct($type)
    {
        $this->_type = $type;
        $this->_mysql = $this->_connect();
    }

    public function getHandle()
    {
        return $this->_mysql;
    }

    private function _connect()
    {
        $config = \Afanty\Config\Env::getConfig('mysql');

        if (empty($config[$this->_type])) {
            throw new \Afanty\Exception\Response("Can't connect Mysql ( {$this->_type} ).");
        }
        
        $config = $config[$this->_type];


        $dsn = 'mysql:' . implode(';', array(
            'host='    . $config['host'],
            'port='    . $config['port'],
            'dbname='  . $config['dbname'],
            'charset=' . $config['charset'],
        ));

        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}",
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        );

        $mysql = new \PDO(
            $dsn, $config['username'], $config['password'], $options
        );

        return $mysql;
    }    
}