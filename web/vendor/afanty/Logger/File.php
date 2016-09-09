<?php

namespace Afanty\Logger;

class File
{

    public static function log($type, $msg)
    {
        $logFile = self::getPath($type);
        $isNewFile = !file_exists($logFile);
        $fp = fopen($logFile, 'a');
        if (flock($fp, LOCK_EX)) {
            if ($isNewFile) {
                chmod($logFile, 0666);
            }
            fwrite($fp, $msg . "\n");
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    protected static function getPath($type)
    {
        $date = date('Ymd');
        $logDir = \Afanty\Config\Env::getConfig('common', 'log_dir');
        
        return $logDir . "/{$type}/{$date}.log";
    }

}
