<?php

namespace Scoop\Database;

use Scoop\Config;
use Scoop\Database\Cache\Statement;

/**
 * Singleton instance mysqli wrapper
 * Class Connection
 * @package Scoop\Database
 */
class Connection {

    /**
     * @var \mysqli_stmt
     */
    public static $lastStatementUsed;

    /**
     * @var Statement
     */
    public $statementCache;

    /**
     * @var int
     */
    private static $affectedRows;

    /**
     * @var string[]
     */
    private static $sqlHistoryArray = [ ];

    /**
     * @var bool
     */
    private static $loggingEnabled = true;

    /**
     * @var Connection
     */
    private static $instance;

    /**
     * @var int
     */
    private static $insertId;

    /**
     * @var \mysqli
     */
    private $mysqli;

    /**
     * Connection constructor.
     *
     * @param $config string[]
     */
    protected function __construct ( $config ) {

        mysqli_report ( MYSQLI_REPORT_STRICT );

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

        $this->statementCache = new Statement();
    }

    /**
     * Connection destructor
     */
    public function __destruct () {

        if ( isset( $this->mysqli ) ) {
            $threadId = $this->mysqli->thread_id;
            $this->mysqli->kill ( $threadId );
            $this->mysqli->close ();
        }
    }

    /**
     * @param $sql
     * @param $queryParams
     *
     * @return bool|\mysqli_result
     * @throws \Exception
     */
    public static function execute ( $sql, $queryParams ) {

        $self = self::get_instance ();

        // log the query
        static::log_sql ( $sql, $queryParams );

        // start sql transaction
        $self->mysqli->begin_transaction ();

        // use cache to get prepared statement
        $statement = $self->get_statement_from_sql ( $sql );

        // bind params
        if ( is_array ( $queryParams ) && !empty( $queryParams ) ) {
            $bindTypes = '';
            foreach ( $queryParams as $name => $value ) {
                $bindTypes .= static::get_bind_type ( $value );
            }

            $statement->bind_param ( $bindTypes, ...$queryParams );

        }

        // execute statement
        if ( !$statement->execute () ) {
            $self->mysqli->rollback ();
            throw new \Exception(
                'MySQL Error Number ( ' . $statement->errno . ' )' . $statement->error . PHP_EOL . $sql . PHP_EOL);
        }

        // commit this transaction
        $self->mysqli->commit ();

        // save info for latest query
        static::$insertId = $statement->insert_id;
        static::$affectedRows = $statement->affected_rows;

        return $statement->get_result ();

    }

    /**
     * @return Connection
     */
    public static function get_instance () {

        if ( !static::is_connected () ) {
            static::connect ();
        }

        return static::$instance;
    }

    /**
     * Connects to db and initializes cache
     * return @void
     */
    public static function connect () {

        if ( !self::is_connected () ) {

            $config = Config::get_db_config ();

            // did we get the file?
            if ( !$config ) {
                throw new \Exception( 'failed to load db credentials' );
            }

            // create a new instance of this class to connect to our db
            static::$instance = new static( $config );
        }

    }

    /**
     * @return bool
     */
    public static function is_connected () {

        return is_a ( static::$instance, __CLASS__ );
    }

    /**
     * @param $sql string
     * @param $queryParams array
     */
    public static function log_sql ( $sql, $queryParams ) {

        if ( static::get_logging_enabled () ) {
            static::$sqlHistoryArray[] = [$sql, $queryParams];
        }

    }

    /**
     * @return bool
     */
    public static function get_logging_enabled () {

        return self::$loggingEnabled;
    }

    /**
     * @param bool $loggingEnabled
     */
    public static function set_logging_enabled ( $loggingEnabled ) {

        self::$loggingEnabled = $loggingEnabled;
    }

    /**
     * @param $sql
     *
     * @return \mysqli_stmt
     * @throws \Exception
     */
    public function get_statement_from_sql ( $sql ) {

        $key = md5 ( $sql );

        if ( empty( $this->statementCache->get ( $key ) ) ) {

            // prepare the statement
            $statement = $this->mysqli->prepare ( $sql );

            if ( $statement === false ) {
                throw new \Exception( 'statment preparation failed' );
            }

            $this->statementCache->set ( $key, $statement );
        }

        return $this->statementCache->get ( $key );

    }

    /**
     * @param $value
     *
     * @return string
     * @throws \Exception
     */
    public static function get_bind_type ( $value ) {

        $valueType = gettype ( $value );

        switch ( $valueType ) {
            case "NULL":
            case "string":
                $bindType = 's';
                break;
            case "integer":
            case "boolean":
                $bindType = 'i';
                break;
            case "double":
                $bindType = 'd';
                break;
            default:
                throw new \Exception( "Query param has type of {$valueType}" );
        }

        return $bindType;
    }

    /**
     * @return int
     */
    public static function get_affected_rows () {

        return static::$affectedRows;
    }

    /**
     * @return int|null
     */
    public static function get_insert_id () {

        return static::$insertId;
    }

    /**
     * @return array
     */
    public static function get_sql_history () {

        return self::$sqlHistoryArray;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     */
    private function __clone () {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     */
    private function __wakeup () {
    }
}
