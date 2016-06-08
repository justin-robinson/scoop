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
    public $lastStatementUsed;

    /**
     * @var Statement
     */
    public $statementCache;

    /**
     * @var int
     */
    private $affectedRows;

    /**
     * @var string[]
     */
    private $sqlHistoryArray = [ ];

    /**
     * @var bool
     */
    private $loggingEnabled = false;

    /**
     * @var int
     */
    private $insertId;

    /**
     * @var \mysqli
     */
    private $mysqli;

    /**
     * Connection constructor.
     *
     * @param $config string[]
     */
    public function __construct ( $config ) {

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

        $this->statementCache = new Statement(500);
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
    public function execute ( $sql, $queryParams ) {

        // log the query
        $this->log_sql ( $sql, $queryParams );

        // start sql transaction
        $this->mysqli->begin_transaction ();

        // use cache to get prepared statement
        $statement = $this->get_statement_from_sql ( $sql );

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
            $this->mysqli->rollback ();
            throw new \Exception(
                'MySQL Error Number ( ' . $statement->errno . ' )' . $statement->error . PHP_EOL . $sql . PHP_EOL);
        }

        // commit this transaction
        $this->mysqli->commit ();

        // save info for latest query
        $this->insertId = $statement->insert_id;
        $this->affectedRows = $statement->affected_rows;

        return $statement->get_result ();

    }

    /**
     * @param $sql string
     * @param $queryParams array
     */
    public function log_sql ( $sql, $queryParams ) {

        if ( $this->get_logging_enabled () ) {
            $this->sqlHistoryArray[] = [$sql, $queryParams];
        }

    }

    /**
     * @return bool
     */
    public function get_logging_enabled () : bool {

        return $this->loggingEnabled;
    }

    /**
     * @param bool $loggingEnabled
     */
    public function set_logging_enabled ( bool $loggingEnabled ) {

        $this->loggingEnabled = $loggingEnabled;
    }

    /**
     * @param $sql
     *
     * @return \mysqli_stmt
     * @throws \Exception
     */
    public function get_statement_from_sql ( $sql ) : \mysqli_stmt {

        $key = md5 ( $sql );

        if ( !$this->statementCache->exists( $key ) ) {

            // prepare the statement
            $statement = $this->mysqli->prepare ( $sql );

            if ( $statement === false ) {
                throw new \Exception( "statment preparation failed: ({$this->mysqli->errno}) {$this->mysqli->error}" . PHP_EOL . $sql );
            }

            $this->statementCache->put ( $key, $statement );
        }

        return $this->statementCache->get ( $key );

    }

    /**
     * @param $value
     *
     * @return string
     * @throws \Exception
     */
    public static function get_bind_type ( $value ) : string {

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
            case "object":
                if ( method_exists($value, '__toString') ) {
                    $bindType = 's';
                    break;
                }
            default:
                throw new \Exception( "Query param has type of {$valueType}" );
        }

        return $bindType;
    }

    /**
     * @return int
     */
    public function get_affected_rows () : int {

        return $this->affectedRows;
    }

    /**
     * @return int|null
     */
    public function get_insert_id () : int {

        return $this->insertId;
    }

    /**
     * @return array
     */
    public function get_sql_history () : array {

        return $this->sqlHistoryArray;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     */
    private function __clone () {}

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     */
    private function __wakeup () {}
}
