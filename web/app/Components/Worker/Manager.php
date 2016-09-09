<?php
namespace Components\Worker;

class Manager implements \Countable
{
    const STDIN  = 0;
    const STDOUT = 1;
    const STDERR = 2;
    const NON_BLOCKING = 0;
    const BLOCKING     = 1;
    private static $DESCRIPTORSPEC = array(
        self::STDIN  => array('pipe', 'r'),
        self::STDOUT => array('pipe', 'w'),
        self::STDERR => array('pipe', 'w'),
    );
    private $workers   = array();
    private $processes = array();
    private $stdins    = array();
    private $stdouts   = array();
    private $stderrs   = array();
    public function attach(Worker $worker)
    {
        $process = proc_open($worker->getCommand(), self::$DESCRIPTORSPEC, $pipes);
        if (false === is_resource($process)) {
            throw new \RuntimeException();
        }
        stream_set_blocking($pipes[self::STDOUT], self::NON_BLOCKING);
        $this->workers[]   = $worker;
        $this->processes[] = $process;
        $this->stdins[]    = $pipes[self::STDIN];
        $this->stdouts[]   = $pipes[self::STDOUT];
        $this->stderrs[]   = $pipes[self::STDERR];
    }
    public function listen($timeout = 200000)
    {
        $read = array();
        $write = [];
        $expect = [];
        foreach ($this->workers as $i => $_) {
            $read[] = $this->stdouts[$i];
            $read[] = $this->stderrs[$i];
        }
        $changed_num = stream_select($read, $write, $expect, 0, $timeout);
        if (false === $changed_num) {
            throw new \RuntimeException();
        }
        if (0 === $changed_num) {
            return;
        }
        foreach ($read as $stream) {
            $i = array_search($stream, $this->stdouts, true);
            if (false === $i) {
                $i = array_search($stream, $this->stderrs, true);
                if (false === $i) {
                    continue;
                }
            }
            $worker = $this->workers[$i];
            $stdout = stream_get_contents($this->stdouts[$i]);
            $stderr = stream_get_contents($this->stderrs[$i]);
            $status = $this->detach($worker);
            if (0 === $status) {
                return $worker->done($stdout, $stderr);
            } else if (0 < $status) {
                return $worker->fail($stdout, $stderr, $status);
            } else {
                throw new \RuntimeException();
            }
        }
    }
    public function detach(Worker $worker)
    {
        $i = array_search($worker, $this->workers, true);
        if (false === $i) {
            throw new \RuntimeException();
        }
        fclose($this->stdins[$i]);
        fclose($this->stdouts[$i]);
        fclose($this->stderrs[$i]);
        $status = proc_close($this->processes[$i]);
        unset($this->workers[$i]);
        unset($this->processes[$i]);
        unset($this->stdins[$i]);
        unset($this->stdouts[$i]);
        unset($this->stderrs[$i]);
        return $status;
    }
    public function count()
    {
        return count($this->workers);
    }
    public function __destruct()
    {
        array_walk($this->stdins, 'fclose');
        array_walk($this->stdouts, 'fclose');
        array_walk($this->stderrs, 'fclose');
        array_walk($this->processes, 'proc_close');
    }
}