<?php

namespace phpr\Database\Model;

use phpr\Database\Cache\Statement;
use phpr\Database\Connection;
use phpr\Database\Rows;

class Generic {

    public static $queryParams = [ ];

    protected $loadedFromDB = false;

    protected $orignalDBValues;

    protected $DBColumnsArray = [ ];

    private static $sqlHistoryArray = [ ];

    /**
     * @var Statement
     */
    public static $statementCache;

    /**
     * Generic constructor.
     * @param array $dataArray
     */
    public function __construct ( $dataArray = [ ] ) {

        // by default all values are null
        $this->orignalDBValues = array_fill_keys (
            array_keys ( $this->DBColumnsArray ),
            null );

        $this->populate ( $dataArray );
    }

    // run a raw sql query
    /**
     * @param $sql
     * @return Rows
     */
    public static function query ( $sql, $queryParams = [ ] ) {

        // log the query
        self::$sqlHistoryArray[] = $sql;

        // start sql transaction
        Connection::begin_transaction ();

        $statement = static::get_statement ( $sql, $queryParams );

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
            trigger_error ( 'MySQL Error Number ( ' . Connection::errno () . ' )' . Connection::error () );
            var_dump ( $sql );
        }

        // get the result
        $result = $statement->get_result ();

        // commit this transaction
        Connection::commit ();

        // format the data if it was a select
        if ( !empty( $result->num_rows ) ) {

            // create a container for the rows
            $rows = new Rows();

            // put all rows in the container
            while ( $row = $result->fetch_assoc () ) {

                $dbObject = new static( $row );

                // make that this came from the DB
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

        $dataArray = (array) $dataArray;

        foreach ( $dataArray as $colName => $colValue ) {
            $this->$colName = $colValue;
        }
    }

    /**
     * @param array $columnsToInclude
     * @return \StdClass
     */
    public function to_stdclass ( array $columnsToInclude = [ ] ) {

        if ( empty( $columnsToInclude ) ) {
            $columnsToInclude = $this->get_column_names ();
        }

        $stdClass = new \StdClass();

        foreach ( $columnsToInclude as $columnName ) {
            $stdClass->$columnName = $this->$columnName;
        }

        return $stdClass;
    }

    /**
     * @return array
     */
    public function get_column_names () {

        return array_keys ( $this->DBColumnsArray );
    }

    /**
     * @return array
     */
    public static function get_sql_history () {

        return self::$sqlHistoryArray;
    }

    /**
     *
     */
    public function loaded_from_database () {

        $this->loadedFromDB = true;

        foreach ( $this->DBColumnsArray as $columnName => $properties ) {
            $this->orignalDBValues[$columnName] = $this->$columnName;
        }

    }

    /**
     * @param $sql
     */
    public static function strip_comments ( $sql ) {
        // TODO implement strip comments function
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