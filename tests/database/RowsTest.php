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

        $this->assertEquals( 0, $this->rows[0]->name, "getting row by array index should work"  );

        $i = $this->lastIndex+1;

        $this->rows[$i] = new Test(['name' => $i]);

        $this->assertEquals( $i, $this->rows[$i]->name, "setting row by array index should work" );

        unset($this->rows[$i]);

        $this->assertNull( $this->rows[$i], "unset on rows should remove row from storage array");
    }
    
    public function test_jsonSerialize () {
        
        $this->assertEquals( json_encode( $this->rowData ), json_encode( $this->rows ), "json encode should encode just the row data" );
    }

}
