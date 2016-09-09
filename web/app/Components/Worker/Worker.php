<?php
namespace Components\Worker;

interface Worker
{
    public function getCommand();
    public function done($stdout, $stderr);
    public function fail($stdout, $stderr, $status);
}