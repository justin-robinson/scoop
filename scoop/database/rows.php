<?php

namespace Scoop\Database;

use Scoop\Database\Model\Generic;

/**
 * Class Rows
 * @package Scoop\Database
 */
class Rows implements \Iterator, \ArrayAccess, \JsonSerializable {

    /**
     * @var int
     */
    public $numRows = 0;

    /**
     * @var $rowsStorageArray Generic[]
     */
    private $rowsStorageArray = [ ];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @return string
     */
    public function __toString () {

        return var_export( $this->rowsStorageArray, true );
    }

    /**
     * Set position to beginning
     */
    public function rewind () {

        $this->position = 0;
    }

    /**
     * @param $row Generic
     */
    public function add_row ( Generic $row ) {

        $this->rowsStorageArray[] = $row;
        ++$this->numRows;
    }

    /**
     * @param int $index
     *
     * @return Generic
     */
    public function get ( int $index ) : Generic {

        if( !array_key_exists( $index, $this->rowsStorageArray ) ) {
            return null;
        }

        return $this->rowsStorageArray[$index];
    }

    /**
     * @return array Model[]
     */
    public function get_rows () : array {

        return $this->rowsStorageArray;
    }

    /**
     * @return Generic
     */
    public function first () : Generic {

        return $this->get(0);
    }

    /**
     * @return bool
     */
    public function is_last_row () : bool {

        return $this->key() === ($this->numRows - 1);
    }

    /**********************************
     * Iterator functions
     **********************************/

    /**
     * get the current position
     * @return int
     */
    public function key () {

        return $this->position;
    }

    /**
     * @return array
     */
    public function to_array () : array {

        $array = [ ];

        foreach ( $this as $row ) {
            $array[] = $row->to_array();
        }

        return $array;
    }

    /**
     * get Model at current index
     * @return Model
     */
    public function current () {

        return $this->rowsStorageArray[$this->position];
    }

    /**
     * go to next item in array
     */
    public function next () {

        ++$this->position;
    }

    /**********************************
     *  ArrayAccess functions
     **********************************/

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet ( $offset, $value ) {

        if( is_null( $offset ) ) {
            $this->rowsStorageArray[] = $value;
        } else {
            $this->rowsStorageArray[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists ( $offset ) {

        return isset($this->rowsStorageArray[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset ( $offset ) {

        unset($this->rowsStorageArray[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return null|Model
     */
    public function offsetGet ( $offset ) {

        return isset($this->rowsStorageArray[$offset]) ? $this->rowsStorageArray[$offset] : null;
    }

    /**
     * @return bool
     */
    public function valid () {

        return isset($this->rowsStorageArray[$this->position]);
    }

    /**********************************
     * JSONSerialize functions
     **********************************/

    /**
     * @return array|Model[]
     */
    public function jsonSerialize () {

        return $this->rowsStorageArray;
    }

}
