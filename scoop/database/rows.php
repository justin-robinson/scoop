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

        return var_export ( $this->rowsStorageArray, true );
    }

    /**
     * @param $row Generic
     */
    public function add_row ( Generic $row ) {

        $this->rowsStorageArray[] = $row;
        ++$this->numRows;
    }

    /**
     * @return Generic
     */
    public function first () {

        return $this->get(0);
    }

    /**
     * @param int $index
     *
     * @return Generic
     */
    public function get ( $index ) {

        if( !array_key_exists( $index, $this->rowsStorageArray ) ) {
            return null;
        }

        return $this->rowsStorageArray[$index];
    }

    /**
     * @return array Model[]
     */
    public function get_rows () {

        return $this->rowsStorageArray;
    }

    /**
     * @return bool
     */
    public function is_last_row () {

        return $this->key () === ( $this->numRows - 1 );
    }

    /**
     * @return array
     */
    public function to_array () {

        $array = [ ];

        foreach ( $this as $row ) {
            $array[] = $row->to_array ();
        }

        return $array;
    }

    /**
     * get Model at current index
     * @return Model
     */

    /**********************************
     * Iterator functions
     **********************************/
    public function current () {

        return $this->rowsStorageArray[$this->position];
    }
    /**
     * get the current position
     * @return int
     */
    public function key () {

        return $this->position;
    }

    /**
     * go to next item in array
     */
    public function next () {

        ++$this->position;
    }

    /**
     * Set position to beginning
     */
    public function rewind () {

        $this->position = 0;
    }

    /**
     * @return bool
     */
    public function valid () {

        return isset( $this->rowsStorageArray[$this->position] );
    }

    /**********************************
     *  ArrayAccess functions
     **********************************/
    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists ( $offset ) {

        return isset( $this->rowsStorageArray[$offset] );
    }

    /**
     * @param mixed $offset
     *
     * @return null|Model
     */
    public function offsetGet ( $offset ) {

        return isset( $this->rowsStorageArray[$offset] ) ? $this->rowsStorageArray[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet ( $offset, $value ) {

        if ( is_null ( $offset ) ) {
            $this->rowsStorageArray[] = $value;
        } else {
            $this->rowsStorageArray[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset ( $offset ) {

        unset( $this->rowsStorageArray[$offset] );
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
