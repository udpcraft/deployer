<?php

namespace Afanty\Response;


class Bin implements IResponse
{
    public function write($result)
    {
        if (is_string($result)) {
            die($result);
        }
        die('');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->_getPayload($result));
        exit();
    }

    private function _getPayload($data, $errno = 0, $errmsg = '')
    {
        return array(
            'errno' => $errno,
            'errmsg' => $errmsg,
            'time' => time(),
            'data' => $data,
        );
    }
}