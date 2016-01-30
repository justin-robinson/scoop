<?php

namespace phpr\Database;

/**
 * Class Rows
 * @package phpr\Database
 */
class Rows implements \Iterator, \ArrayAccess, \JsonSerializable {

    /**
     * @var int
     */
    public $numRows;

    /**
     * @var $rowsStorageArray Model[]
     */
    private $rowsStorageArray;

    /**
     * @var int
     */
    private $position;

    /**
     * Rows constructor.
     */
    public function __construct () {

        // initialize array storage
        $this->rowsStorageArray = [ ];
        $this->numRows = 0;
        $this->rewind ();

    }

    /**
     * @param $row Model
     */
    public function addRow ( $row ) {

        $this->rowsStorageArray[] = $row;
        $this->numRows++;
    }

    /**
     * @return array Model[]
     */
    public function getRows () : array {

        return $this->rowsStorageArray;
    }

    /**
     * @return bool
     */
    public function isLastRow () {

        return $this->key () === ( $this->numRows - 1 );
    }

    /**
     * @param array $columnsToInclude
     * @return array
     */
    public function to_array ( array $columnsToInclude = [ ] ) {

        $array = [ ];

        foreach ( $this as $row ) {
            $array[] = $row->to_stdclass ( $columnsToInclude );
        }

        return $array;
    }

    /**********************************
     * Iterator functions
     **********************************/

    /**
     * Set position to beginning
     */
    public function rewind () {

        $this->position = 0;
    }

    /**
     * get Model at current index
     * @return Model
     */
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

    /**********************************
     *  ArrayAccess functions
     **********************************/

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
     * @return bool
     */
    public function offsetExists ( $offset ) {

        return isset( $this->rowsStorageArray[$offset] );
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset ( $offset ) {

        unset( $this->rowsStorageArray[$offset] );
    }

    /**
     * @param mixed $offset
     * @return null|Model
     */
    public function offsetGet ( $offset ) {

        return isset( $this->rowsStorageArray[$offset] ) ? $this->rowsStorageArray[$offset] : null;
    }

    /**
     * @return bool
     */
    public function valid () {

        return isset( $this->rowsStorageArray[$this->position] );
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