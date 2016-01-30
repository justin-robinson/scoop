<?php

namespace phpr\Database;

/**
 * Class Connection
 * @package phpr\Database
 */
class Connection {

    /**
     * @var \mysqli
     */
    private static $mysqli;

    /**
     * @var \mysqli_stmt
     */
    private static $lastStatementUsed;

    /**
     * Connects to database
     * @throws Error
     */
    static function connect () {

        $config = \phpr\Config::get_db_config ();

        // did we get the file?
        if ( $config ) {

            // attempt to connect to the db
            self::$mysqli = new \mysqli(
                $config['host'],
                $config['user'],
                $config['password'],
                '',
                $config['port'] );

            // die on error
            if ( self::$mysqli->connect_error ) {
                die( 'Connect Error (' . self::$mysqli->connect_errno . ') '
                    . self::$mysqli->connect_error );
            }

            // we will manually commit our sql changes
            self::autocommit ( false );

        } else {
            throw new Error( 'failed to load db credentials' );
        }

    }

    /**
     * Closes mysqli connection
     */
    static function disconnect () {

        if ( !self::close () ) {
            die( 'Error closing connection' );
        }
    }


    /**
     * pass all missing static function calls the $mysqli resource
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic ( $name, $arguments ) {

        // does the unimplemented function exist on the mysqli resource?
        if ( method_exists ( self::$mysqli, $name ) ) {

            // well call it!
            $return = call_user_func_array (
                [
                    self::$mysqli,
                    $name
                ],
                $arguments );
        } // how about a property on the mysqli resource?
        else if ( isset( self::$mysqli->$name ) ) {
            $return = self::$mysqli->$name;
        } else {
            $return = null;
        }

        return $return;
    }

    /**
     * @param $statement \mysqli_stmt
     */
    public static function set_last_statement_used ( $statement ) {

        static::$lastStatementUsed = $statement;
    }

    /**
     * @return int|null
     */
    public static function get_insert_id () {

        if ( isset( static::$lastStatementUsed->insert_id ) ) {
            $insertId = static::$lastStatementUsed->insert_id;
        } else {
            $insertId = null;
        }

        return $insertId;
    }
}

?>
