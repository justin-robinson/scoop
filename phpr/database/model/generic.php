<?php

namespace phpr\Database\Model;

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
     *
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
     *
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
     *
     * @param       $sql
     * @param array $queryParams
     *
     * @return bool|int|Rows
     * @throws \Exception
     */
    public static function query ( $sql, $queryParams = [ ] ) {

        $result = Connection::execute ( $sql, $queryParams );

        // format the data if it was a select
        if ( $result && !empty( $result->num_rows ) ) {

            // create a container for the rows
            $rows = new Rows();

            // put all rows in the container
            while ( $row = $result->fetch_assoc () ) {

                // create a new instance of this model
                $dbObject = new static( $row );

                // mark that this came from the DB
                $dbObject->loaded_from_database ();

                $rows->addRow ( $dbObject );

            }

        } else if ( !empty( Connection::get_affected_rows () ) ) {
            $rows = Connection::get_affected_rows ();
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
     *
     * @param $dataArray array
     */
    public function populate ( $dataArray ) {

        $this->dBValuesArray = (array) $dataArray;

    }

    /**
     * @return array
     */
    public function to_array () : array {

        return $this->dBValuesArray;
    }

    /**
     * @return array
     */
    public function get_column_names () : array {

        return array_keys ( $this->orignalDbValuesArray );
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
