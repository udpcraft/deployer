<?php
namespace Components\Worker;

class Command implements Worker
{
    private $_command;
    public function __construct($command)
    {
        $this->_command = $command;
    }
    public function getCommand()
    {
        return $this->_command;
    }
    public function done($stdout, $stderr)
    {
        return str_replace(array("\r\n", "\n", "\r"), ' ', var_export(array(
            'command' => $this->getCommand(),
            'stdout'  => $stdout,
            'stderr'  => $stderr,
        ), true));
    }
    public function fail($stdout, $stderr, $status)
    {
        return str_replace(array("\r\n", "\n", "\r"), ' ', var_export(array(
            'command' => $this->getCommand(),
            'stdout'  => $stdout,
            'stderr'  => $stderr,
            'status'  => $status,
        ), true));
    }
}

// $manager = new WorkerManager();

// $ips = array('192.168.1.2033', '192.168.1.251','192.168.1.203', '192.168.1.251','192.168.1.203', '192.168.1.251');

// foreach ($ips as $ip) {
//     $manager->attach(new SleepThenEcho($ip));
// }

// while (0 < count($manager)) {
//     $manager->listen();
// }