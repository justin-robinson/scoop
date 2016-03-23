<?php

namespace Scoop\Database\Cache;

use LRUCache\LRUCache;

/**
 * Class Statement
 * @package Scoop\Database\Cache
 */
class Statement {

    /**
     * @var $cache LRUCache
     */
    private $cache;

    /**
     * Statement constructor.
     */
    public function __construct () {

        $this->cache = new LRUCache(500);
    }

    /**
     *
     */
    public function __destruct () {

        $this->cache->walkCache(function(&$statement){
            $statement->close();
        });
    }

    /**
     * @param $key
     *
     * @return \mysqli_stmt|null
     */
    public function get ( $key ) {

        return $this->cache->get($key);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function exists ( $key ) {

        return is_null($this->cache->get($key));
    }

    /**
     * @param $key string
     * @param $value \mysqli_stmt
     *
     * @return bool
     */
    public function set ( $key, $value ) {

        $isMysqliStatement = is_a ( $value, 'mysqli_stmt' );

        if ( $isMysqliStatement ) {
            $this->cache->put($key, $value);
        }

        return $isMysqliStatement;
    }
}
