<?php

namespace phpr\Database;
use phpr\Environment;
use phpr\Config;

/**
 * Class Connection
 * @package phpr\Database
 */
class Connection {

    /**
     * @var Connection
     */
    private static $instance;

    /**
     * @var \mysqli
     */
    private $mysqli;

    /**
     * @var \mysqli_stmt
     */
    private static $lastStatementUsed;

    /**
     * Connection constructor.
     */
    protected function __construct () {

        $config = Config::get_db_config ();

        // did we get the file?
        if ( $config ) {

            mysqli_report ( MYSQLI_REPORT_STRICT );

            try {
                // attempt to connect to the db
                $this->mysqli = new \mysqli(
                    $config['host'],
                    $config['user'],
                    $config['password'],
                    '',
                    $config['port'] );

                // die on error
                if ( $this->mysqli->connect_error ) {
                    die( 'Connect Error (' . $this->mysqli->connect_errno . ') '
                        . $this->mysqli->connect_error );
                }

                // we will manually commit our sql changes
                $this->mysqli->autocommit ( false );
            } catch ( \mysqli_sql_exception $e ) {
                if ( Environment::constant_is_defined_and_equals ( 'NO_DB_CONNECT' ) ) {
                    // ignore it
                } else {
                    throw $e;
                }
            }

        } else {
            throw new \Exception( 'failed to load db credentials' );
        }
    }

    /**
     * Connection destructor
     */
    public function __destruct () {

        $threadId = $this->mysqli->thread_id;
        $this->mysqli->kill ( $threadId );
        $this->mysqli->close ();
    }

    /**
     * @return Connection
     */
    public static function get_instance () {

        if ( empty( static::$instance ) ) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param $statement \mysqli_stmt
     */
    public static function set_last_statement_used ( &$statement ) {

        static::$lastStatementUsed = &$statement;
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

    /**
     * pass all missing static function calls the $mysqli resource
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic ( $name, $arguments ) {

        $instance = static::get_instance ();

        // does the unimplemented function exist on the mysqli resource?
        if ( method_exists ( $instance->mysqli, $name ) ) {

            // well call it!
            $return = call_user_func_array (
                [
                    $instance->mysqli,
                    $name
                ],
                $arguments );
        } // how about a property on the mysqli resource?
        else if ( isset( $instance->mysqli->$name ) ) {
            $return = $instance->mysqli->$name;
        } else {
            $return = null;
        }

        return $return;
    }

}

?>
