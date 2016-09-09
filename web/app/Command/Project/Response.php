<?php

namespace Command\Project;

class Response extends \Afanty\Helper\Singleton
{
    public function output($obj, $status = 1)
    {
        $res = [];
        $res['status'] = $status;
        $res['msg'] = join('', array_slice(explode('\\', get_class($obj)), -1));

        return $res;
    }
}

