<?php

namespace phpr\Database\Model;

use phpr\Database\Cache\Statement;
use phpr\Database\Connection;
use phpr\Database\Rows;

/**
 * Class Generic
 * @package phpr\Database\Model
 */
class Generic {

    /**
     * instance specific model values
     */
    protected $dBValuesArray = [ ];

    /**
     * @var array
     */
    protected $orignalDbValuesArray;

    /**
     * @var array
     */
    protected static $dBColumnPropertiesArray = [ ];

    /**
     * @var array
     */
    protected static $dBColumnDefaultValuesArray = [ ];

    /**
     * @var bool
     */
    protected $loadedFromDb = false;

    /**
     * @var string[]
     */
    private static $sqlHistoryArray = [ ];

    /**
     * @var Statement
     */
    public static $statementCache;

    /**
     * @var \mysqli_stmt
     */
    public static $lastStatementUsed;

    /**
     * Generic constructor.
     * @param array $dataArray
     */
    public function __construct ( $dataArray = [ ] ) {

        // by default all values are null
        $this->orignalDbValuesArray = static::$dBColumnDefaultValuesArray;

        // have to have a way to stop the populate since mysqli->fetch_object runs our __set before our __construct
        if ( is_array ( $dataArray ) || is_object ( $dataArray ) ) {
            $dataArray = array_replace ( static::$dBColumnDefaultValuesArray, (array) $dataArray );

            $this->populate ( $dataArray );

        }

    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get ( $name ) {

        if ( isset( $this->dBValuesArray[$name] ) ) {
            $property = $this->dBValuesArray[$name];
        } else {
            $property = null;
        }

        return $property;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set ( $name, $value ) {

        $this->dBValuesArray[$name] = $value;
    }

    /**
     * run a raw sql query
     * @param $sql
     * @param array $queryParams
     * @return bool|int|Rows
     * @throws \Exception
     */
    public static function query ( $sql, $queryParams = [ ] ) {

        // log the query
        self::$sqlHistoryArray[] = $sql;

        // start sql transaction
        Connection::begin_transaction ();

        $statement = static::get_statement ( $sql );

        // bind params
        if ( is_array ( $queryParams ) && !empty( $queryParams ) ) {
            $bindTypes = '';
            foreach ( $queryParams as $name => $value ) {
                $bindTypes .= self::get_bind_type ( $value );
            }

            $statement->bind_param ( $bindTypes, ...$queryParams );

        }

        // execute statement
        if ( !$statement->execute () ) {
            Connection::rollback ();
            trigger_error ( 'MySQL Error Number ( ' . $statement->errno . ' )' . $statement->error );
            var_dump ( $sql );
        }

        // get the result
        $result = $statement->get_result ();

        // commit this transaction
        Connection::commit ();

        // save info for latest query
        Connection::set_last_statement_used ( $statement );

        // format the data if it was a select
        if ( $result && !empty( $result->num_rows ) ) {

            // create a container for the rows
            $rows = new Rows();

            // put all rows in the container
            while ( $dbObject = $result->fetch_object ( get_called_class (), [ -1 ] ) ) {

                // mark that this came from the DB
                $dbObject->loaded_from_database ();

                $rows->addRow ( $dbObject );

            }

        } else if ( !empty( $statement->affected_rows ) ) {
            $rows = $statement->affected_rows;
        } else {
            $rows = false;
        }

        if ( method_exists ( $result, 'free' ) ) {
            $result->free ();
        }

        return $rows;

    }

    /**
     * generate a new instance of this class from an associative array
     * @param $dataArray array
     */
    public function populate ( $dataArray ) {

        $this->dBValuesArray = (array) $dataArray;

    }

    /**
     * @param array $columnsToInclude
     * @return \StdClass
     */
    public function to_stdclass ( array $columnsToInclude = [ ] ) {

        if ( empty( $columnsToInclude ) ) {
            $columnsToInclude = $this->get_column_names ();
        }

        $stdClass = new \stdClass();

        foreach ( $columnsToInclude as $columnName ) {
            $stdClass->$columnName = $this->$columnName;
        }

        return $stdClass;
    }

    /**
     * @return array
     */
    public function get_column_names () {

        return array_keys ( $this->dBColumnPropertiesArray );
    }

    /**
     * @return array
     */
    public static function get_sql_history () {

        return self::$sqlHistoryArray;
    }

    /**
     * Mark this object as loaded from the database
     */
    public function loaded_from_database () {

        $this->loadedFromDb = true;

        $this->orignalDbValuesArray = $this->dBValuesArray;

    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    public static function get_bind_type ( $value ) {

        $valueType = gettype ( $value );

        switch ( $valueType ) {
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
     * @param $sql
     * @return \mysqli_stmt|null
     * @throws \Exception
     */
    public static function get_statement ( $sql ) {

        $key = md5 ( $sql );

        if ( empty( self::$statementCache->get ( $key ) ) ) {

            // prepare the statement
            $statement = Connection::prepare ( $sql );

            self::$statementCache->set ( $key, $statement );
        }

        return self::$statementCache->get ( $key );

    }

}


?>