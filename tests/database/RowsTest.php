<?php

use DB\Scoop\Test;
use Scoop\Database\Rows;

/**
 * Class RowsTest
 */
class RowsTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Rows
     */
    private $rows;

    /**
     * @var array
     */
    private $rowData;

    /**
     * @var int
     */
    private $lastIndex = 10;

    public function __construct () {

        foreach ( range(0,$this->lastIndex) as $i ) {
            $this->rowData[$i] = new Test(['name' => $i]);
        }

        $this->rows = new Rows();
        foreach ( $this->rowData as $row ) {
            $this->rows->add_row( $row );
        }
    }

    public function test___toString () {

        $expected = "array (
  0 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 0,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  1 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 1,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  2 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 2,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  3 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 3,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  4 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 4,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  5 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 5,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  6 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 6,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  7 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 7,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  8 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 8,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  9 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 9,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
  10 => 
  DB\Scoop\Test::__set_state(array(
     'dBValuesArray' => 
    array (
      'id' => NULL,
      'name' => 10,
      'dateTimeAdded' => 'CURRENT_TIMESTAMP',
    ),
     'loadedFromDb' => false,
     'orignalDbValuesArray' => NULL,
  )),
)";

        $this->assertEquals($expected, (string)$this->rows);
    }

    public function test_add_row () {

        $rows = new Rows();

        $rows->add_row( new Test() );

        $this->assertEquals( 1, $rows->get_num_rows(), "adding a row should increment the count by 1" );
    }

    public function test_first() {

        $this->assertEquals( 0, $this->rows->first()->name, "first should return the first row added" );
    }

    public function test_get() {

        foreach ( range(0,10) as $i) {
            $this->assertEquals( $i, $this->rows->get( $i )->name, "get should return the correct index" );
        }

        $this->assertNull($this->rows->get(++$i), "get should return null if the index dne");
    }

    public function test_get_rows () {

        foreach ( $this->rows->get_rows() as $i => $row ) {
            $this->assertEquals( $i, $row->name );
        }
    }

    public function test_get_num_rows () {

        $rows = new Rows();

        $this->assertEquals( 0, $rows->get_num_rows(), "rows should initialize empty" );

        $rows->add_row( new Test() );

        $this->assertEquals( 1, $rows->get_num_rows(), "add_row should increment the count by 1" );

        $rows[1] = new Test();

        $this->assertEquals( 2, $rows->get_num_rows(), "setting an undefined array index should increment the count by 1" );

        $rows[1] = new Test();
        $this->assertEquals( 2, $rows->get_num_rows(), "setting a defined array index should not increment the count" );

        unset($rows[1]);
        $this->assertEquals( 1, $rows->get_num_rows(), "unsetting a row index should decrement the count by 1" );
    }

    public function test_is_last_row () {

        foreach ( $this->rows as $i => $row ) {

            if ( $i === $this->lastIndex ) {
                $this->assertTrue( $this->rows->is_last_row(), "this should be the last row" );
            } else {
                $this->assertFalse( $this->rows->is_last_row(), "this isn't the last row" );
            }
        }

        $this->rows['stringIndex'] = new Test();

        foreach ( $this->rows as $i => $row ) {
            if ( $i === 'stringIndex' ) {
                $this->assertTrue( $this->rows->is_last_row(), "is last row should work with non-sequential non-numerical indices" );
            }
        }

        unset($this->rows['stringIndex']);
    }

    public function test_to_array () {

        foreach ( $this->rows->to_array() as $i => $row ) {
            $this->assertEquals( $this->rowData[$i]->name, $row['name'] );
        }
    }

    public function test_iterator () {

        $rows = new Rows();

        $numRows = 0;

        foreach ( range(0,10) as $i ) {
            $rows->add_row(new \Scoop\Database\Model\Generic(['v'=>$i]));
            $numRows++;
        }

        foreach ( $rows as $i => $row ) {

            $this->assertEquals( $i, $row->v );

            if ( $i > 5 ) {
                break;
            }
        }

        foreach ( $rows as $i => $row ) {

            if ( $i <= 5 ) {
                continue;
            }

            $this->assertEquals( $i, $row->v );
        }

        $rowsIteratedOver = 0;
        foreach ( $rows as $i => $row ) {

            $this->assertEquals( $i, $row->v );
            ++$rowsIteratedOver;
        }

        $this->assertEquals($numRows, $rowsIteratedOver, "iterator should iterate over all rows");
    }

    public function test_arrayAccess () {

        $rows = new Rows();

        $numRows = 0;

        foreach ( range(0,10) as $i ) {
            $rows->add_row(new Test(['name'=>$i]));
            $numRows++;
        }

        $this->assertEquals( 0, $rows[0]->name, "getting row by array index should work"  );

        ++$i;

        $this->assertFalse(isset($rows[$i]), "new offset shouldn't exist yet");
        $rows[$i] = new Test(['name' => $i]);
        $this->assertTrue(isset($rows[$i]), "setting an offset should work if it's a generic class");

        $rows[$i+1] = new stdClass();
        $this->assertFalse(isset($rows[$i+1]), "setting an offset should fail if it's not a generic class");

        ++$i;
        $this->assertFalse(isset($rows[$i]), "new offset shouldn't exist yet");
        $rows[] = new Test(['name' => $i]);
        $this->assertTrue(isset($rows[$i]), "appending to the rows class should append to the internal storage array");

        $this->assertEquals( $i, $rows[$i]->name, "setting row by array index should work" );

        unset($rows[$i]);
        $this->assertNull( $rows[$i], "unset on rows should remove row from storage array");
    }
    
    public function test_jsonSerialize () {
        
        $this->assertEquals( json_encode( $this->rowData ), json_encode( $this->rows ), "json encode should encode just the row data" );
    }

}
