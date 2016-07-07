<?php

namespace Scoop;

/**
 * Class EventPool
 * @package Scoop
 */
class EventPool {

    /**
     * @var array
     */
    protected $events;

    /**
     * EventPool constructor.
     */
    public function __construct() {
        $this->events = [];
    }

    /**
     * @param string   $name
     * @param callable $callback
     */
    public function on ( string $name, callable $callback ) {
        $this->events[$name][] = $callback;
    }

    /**
     * @param string $name
     * @param array  ...$args
     */
    public function trigger ( string $name, ...$args) {
        if ( empty($this->events[$name]) ) {
            return;
        }

        foreach ( $this->events[$name] as $callback ) {
            $callback(...$args);
        }
    }

}
