<?php

namespace phpr\Database;

class Rows implements \Iterator, \ArrayAccess, \JsonSerializable {

    public $numRows;

    private $rowsStorageArray;

    private $position;

    public function __construct () {

        // initialize array storage
        $this->rowsStorageArray = [];
        $this->numRows = 0;
        $this->rewind();

    }

    public function addRow ( $row ) {
        array_push($this->rowsStorageArray, $row);
        $this->numRows++;
    }

    public function getRows () : array {
        return $this->rowsStorageArray;
    }

    public function isLastRow() {
        return $this->key() === ($this->numRows - 1);
    }

    public function to_array ( array $columnsToInclude = [] ) {
        $array = [];

        foreach ( $this as $row ) {
            $array[] = $row->to_stdclass($columnsToInclude);
        }

        return $array;
    }

    /* Iterator functions */

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->rowsStorageArray[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    /* ArrayAccess functions */

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->rowsStorageArray[] = $value;
        } else {
            $this->rowsStorageArray[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->rowsStorageArray[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->rowsStorageArray[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->rowsStorageArray[$offset]) ? $this->rowsStorageArray[$offset] : null;
    }

    public function valid() {
        return isset($this->rowsStorageArray[$this->position]);
    }

    /* JSONSerialize functions */
    public function jsonSerialize() {
        return $this->rowsStorageArray;
    }

}