<?php

namespace Scoop\Database\Cache;

use LRUCache\LRUCache;

/**
 * Class Statement
 * @package Scoop\Database\Cache
 */
class Statement extends LRUCache {

    /**
     *
     */
    public function __destruct () {

        foreach ( $this as $statement ) {
            $statement->close();
        }
    }

    /**
     * @param $key string
     * @param $value \mysqli_stmt
     *
     * @return bool
     */
    public function put ( $key, $value ) : bool {

        $isMysqliStatement = is_a ( $value, \mysqli_stmt::class );

        if ( $isMysqliStatement ) {
            parent::put($key, $value);
        }

        return $isMysqliStatement;
    }
}
