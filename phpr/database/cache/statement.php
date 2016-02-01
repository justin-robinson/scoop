<?php

namespace phpr\Database\Cache;

/**
 * Class Statement
 * @package phpr\Database\Cache
 */
class Statement {

    /**
     * @var $cache \mysqli_stmt[]
     */
    private $cache;

    /**
     * Statement constructor.
     */
    public function __construct () {

        $this->cache = [ ];
    }

    /**
     *
     */
    public function __destruct () {

        foreach ( $this->cache as $statement ) {
            $statement->close ();
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function exists ( $key ) : bool {

        return isset( $this->cache[$key] );
    }

    /**
     * @param $key
     * @return \mysqli_stmt|null
     */
    public function get ( $key ) {

        return self::exists ( $key ) ? $this->cache[$key] : null;
    }

    /**
     * @param $key
     * @param $value \mysqli_stmt
     */
    public function set ( $key, $value ) {

        if ( is_a ( $value, 'mysqli_stmt' ) ) {
            $this->cache[$key] = $value;
        }
    }
}