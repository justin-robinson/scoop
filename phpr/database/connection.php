<?php

namespace phpr\Database;

class Connection {

    // the mysqli resource
    private static $mysqli;

    // call this to initiate a db connection
    static function connect() {

       $config = \phpr\Config::get_db_config();

        // did we get the file?
        if ( $config ) {

            // attempt to connect to the db
            self::$mysqli = new \mysqli(
                $config['host'],
                $config['user'],
                $config['password'],
                '',
                $config['port']);

            // die on error
            if ( self::$mysqli->connect_error ) {
                die( 'Connect Error (' . self::$mysqli->connect_errno . ') '
                    . self::$mysqli->connect_error);
            }

            // we will manually commit our sql changes
            self::autocommit(false);

        } else {
            throw new Error('failed to load db credentials');
        }

    }

    // call this to close the connection
    static function disconnect () {

        if ( ! self::close() ) {
            die( 'Error closing connection' );
        }
    }

    // pass all missing static function calls the $mysqli resource
    public static function __callStatic( $name, $arguments ) {

        // does the unimplemented function exist on the mysqli resource?
        if ( method_exists( self::$mysqli, $name ) ) {

            // well call it!
            return call_user_func_array(
                array(
                    self::$mysqli,
                    $name
                ),
                $arguments);
        }

        // how about a property on the mysqli resource?
        if ( isset(self::$mysqli->$name) ) {
            return self::$mysqli->$name;
        }
    }
}
?>
