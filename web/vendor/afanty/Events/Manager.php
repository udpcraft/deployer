<?php

namespace Afanty\Events;


class Manager
{
    private static $_events = [];
    
    public static function add($eventName)
    {
        self::$_events[] = $eventName;
    }

    public static function handle()
    {
        foreach (self::$_events as $eventClass) {
            $event = new $eventClass;
            
            if (! $event instanceof \Afanty\Events\Event) {
                throw new \Afanty\Exception\Request("unknow event [$eventClass]");
            }

            $event->raise();
        }
    }
}