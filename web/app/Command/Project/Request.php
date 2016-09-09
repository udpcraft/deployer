<?php

namespace Command\Project;

class Request extends \Afanty\Helper\Singleton
{
    private $_request = [];

    public function set(Array $params)
    {
        foreach ($params as $key => $val)
        {
            $this->_request[$key] = $val;
        }
    }

    public function get($key)
    {
        if (empty($this->_request[$key])) {
            throw new \Exception("unkonow project command request [$key]");
        }
        return $this->_request[$key];
    }
    
}

