<?php

namespace Afanty\Response;


class Json implements IResponse
{
    public function write($result)
    {
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