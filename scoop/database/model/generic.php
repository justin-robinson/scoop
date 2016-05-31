<?php

namespace Scoop\Database\Model;

use Scoop\Database\Connection;
use Scoop\Database\Rows;

/**
 * Class Generic
 * @package Scoop\Database\Model
 */
class Generic implements \JsonSerializable {

    /**
     * @var array
     */
    protected static $dBColumnDefaultValuesArray = [ ];

    /**
     * @var array
     */
    protected static $dBColumnPropertiesArray = [ ];

    /**
     * instance specific model values
     */
    protected $dBValuesArray = [ ];

    /**
     * @var bool
     */
    protected $loadedFromDb = false;

    /**
     * @var array
     */
    protected $orignalDbValuesArray;

    /**
     * Generic constructor.
     *
     * @param array $dataArray
     */
    public function __construct ( array $dataArray = [ ] ) {

        $this->populate ( $dataArray );

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

            // put all rows in the collection
            while ( $row = $result->fetch_object(static::class) ) {

                // add a new instance of this row to the collection
                $rows->add_row ( $row->loaded_from_database () );

            }

        } else if ( Connection::get_affected_rows () >= 1 ) {
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
     *
     * @return bool
     */
    public function __isset ( $name ) {

        return isset($this->dBValuesArray[$name]);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set ( $name, $value ) {

        $this->dBValuesArray[$name] = $value;
    }

    /**
     * @return string
     */
    public function __toString () {
        return var_export($this->dBValuesArray, true);
    }

    /**
     * @return array
     */
    public function get_column_names () {

        return array_keys ( $this->dBValuesArray );
    }

    /**
     * get column names of the model
     * @return array
     */
    public function get_db_values_array () {

        return $this->dBValuesArray;
    }

    /**
     * @return bool
     */
    public function is_loaded_from_database () {

        return $this->loadedFromDb;
    }

    /**
     * Mark this object as loaded from the database
     */
    public function loaded_from_database () {

        $this->loadedFromDb = true;

        $this->orignalDbValuesArray = $this->dBValuesArray;

        return $this;

    }

    /**
     * replace object values with passed ones
     *
     * @param $dataArray array
     */
    public function populate ( array $dataArray ) {

        $this->dBValuesArray = array_replace ( $this->dBValuesArray, $dataArray );
    }

    /**
     * @return array
     */
    public function to_array () {

        return $this->dBValuesArray;
    }

    /**
     * JsonSerializable
     */
    /**
     * @return array
     */
    public function jsonSerialize () {

        return $this->dBValuesArray;
    }

}
