<?php

namespace Afanty\Db;


class Sqlite extends \Afanty\Helper\Singleton
{
    private $_sqlite = null;

    private $_type = null;

    protected function __construct($type = '')
    {
        $this->_type = $type;
        $this->_sqlite = $this->_connect();
    }

    public function getHandle()
    {
        return $this->_sqlite;
    }

    private function _connect()
    {

        $dbFile = \Afanty\Config\Env::getConfig('sqlite', 'file');
        $sqlite = new \PDO("sqlite:$dbFile");

        $sqlite->setAttribute(\PDO::ATTR_ERRMODE, 
                               \PDO::ERRMODE_EXCEPTION);

        return $sqlite;
    }    
}