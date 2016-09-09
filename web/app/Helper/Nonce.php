<?php

namespace Helper;

class Nonce
{
    const RAND_SOURCE = '0123456789abcdefghijklmnopqrstuvwxyz';

    public static function getSalt($length = 6)
    {
        return self::_getRandStr($length);
    }

    private static function _getRandStr($len)
    {
        $str = self::RAND_SOURCE;
        $strLen = strlen($str)-1;
        str_shuffle($str);
        $s = '';
        for($i=0; $i < $len; $i++) {
            $s .= $str[rand(0, $strLen)];
        }
        return $s;
    }

}