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
     * @param          $name
     * @param callable $callback
     */
    public function on ( string $name, callable $callback ) {
        $this->events[$name][] = $callback;
    }

    /**
     * @param $name
     */
    public function trigger ( string $name ) {
        if ( empty($this->events[$name]) ) {
            return;
        }

        foreach ( $this->events[$name] as $callback ) {
            $callback();
        }
    }

}
