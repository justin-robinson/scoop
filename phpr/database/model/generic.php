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
     * Generic constructor.
     * @param array $dataArray
     */
    public function __construct ( array $dataArray = [ ] ) {

        // by default all values are null
        $this->orignalDbValuesArray = static::$dBColumnDefaultValuesArray;

        // populate default values with passed ones
        $dataArray = array_replace ( static::$dBColumnDefaultValuesArray, $dataArray );

        $this->populate ( $dataArray );

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
        Connection::log_sql($sql);

        // start sql transaction
        Connection::begin_transaction ();

        // use cache to get prepared statement
        $statement = Connection::get_statement ( $sql );

        // bind params
        if ( is_array ( $queryParams ) && !empty( $queryParams ) ) {
            $bindTypes = '';
            foreach ( $queryParams as $name => $value ) {
                $bindTypes .= Connection::get_bind_type ( $value );
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
            while ( $row = $result->fetch_assoc() ) {

                // create a new instance of this model
                $dbObject = new static($row);

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
     * @return array
     */
    public function to_array ( ) : array {

        return $this->dBValuesArray;
    }

    /**
     * @return array
     */
    public function get_column_names () : array {

        return array_keys ( $this->orignalDbValuesArray );
    }

    /**
     * @return array
     */
    public static function get_sql_history () : array {

        return self::$sqlHistoryArray;
    }

    /**
     * Mark this object as loaded from the database
     */
    public function loaded_from_database () {

        $this->loadedFromDb = true;

        $this->orignalDbValuesArray = $this->dBValuesArray;

    }

}


?>