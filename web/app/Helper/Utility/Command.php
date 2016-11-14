<?php

namespace Helper\Utility;


class Command
{
    public static function system($command, $errorMsg = '', $checkReturn = true)
    {
        exec($command, $res, $reval);

        $exceptionMsg = "execute command [$command] falied";
        if ($errorMsg) {
            $exceptionMsg = $errorMsg;
        }

        if ($checkReturn && $reval != 0) {
            throw new \Exception($exceptionMsg);
        }

        return $res;
    }

    public static function gitSshProxy()
    {
        return dirname(\Afanty\Config\Env::getRootDir()) . "/shell/ssh_proxy.sh";
    }

    public static function disableStrictHostKeyCheckOption()
    {
        return '-o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no';
    }

    public static function doSudo($user, $command)
    {
        return "sudo -u {$user} -H bash -c '$command'";
    }
}
