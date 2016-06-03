<?php

namespace Scoop\Database\Cache;

use LRUCache\LRUCache;

/**
 * Class Statement
 * @package Scoop\Database\Cache
 */
class Statement extends LRUCache {

    /**
     * Statement constructor.
     */
    public function __construct () {

        parent::__construct( 500 );
    }

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

        $isMysqliStatement = is_a ( $value, 'mysqli_stmt' );

        if ( $isMysqliStatement ) {
            parent::put($key, $value);
        }

        return $isMysqliStatement;
    }
}
