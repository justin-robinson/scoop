<?php

use DB\Scoop\Test;
use Scoop\Database\Model;

/**
 * Class ModelTest
 */
class ModelTest extends PHPUnit_Framework_TestCase {

    public function test_fetch_one () {

        $test = Test::fetch_one();

        $this->assertEquals( Test::class, get_class($test), "fetch one should return a row" );
    }

    public function test_get_sql_table_name () {

        $this->assertEquals( '`scoop`.`test`', Test::get_sql_table_name(), "wrong sql table name returned" );
    }

    public function test_fetch_by_id () {

        $test = Test::fetch_one();
        
        $test2 = Test::fetch_by_id( $test->id );

        $this->assertEquals( $test->id, $test2->id, "fetch by id should return a row with that ID" );
    }

    public function test_fetch_one_where () {

        $test = Test::fetch_one_where( 'name != ?', [''] );

        $this->assertEquals( Test::class, get_class($test) );
        $this->assertNotEmpty( $test->name );
    }

    public function test_fetch_where () {

        $tests = Test::fetch_where( 'id > ? AND name != ?', [0, ''], 5 );

        $this->assertEquals( Scoop\Database\Rows::class, get_class($tests), 'fetch where should return a collection of rows' );
        
        $this->assertEquals( $tests->get_num_rows(), 5 );
    }

    public function test___set () {

        $test = new Test();

        $name = 'blah';

        $test->name = $name;

        $this->assertEquals($name, $test->get_db_values_array()['name'], "setting a db column value should go to the dbValuesArray");

        $dummy = 'dummy';

        $test->dummy = $dummy;

        $this->assertFalse( isset($test->get_db_values_array()['dummy']), "non db column values should not be put in the dbValuesArray");
        $this->assertEquals( $test->dummy, $dummy, "non db column values shoud be attached directly to the model" );
    }

    public function test_delete () {

        $test = new Test();
        $test->name = 'inserted from phpunit';
        $test->save();

        $id = $test->id;

        $test->delete();

        $test = Test::fetch_by_id( $id );

        $this->assertFalse ($test, "delete should delete from the db");
    }

    public function test_save () {

        $test = new Test();
        $test->name = 'inserted from phpunit';
        $test->save();

        $this->assertNotEmpty( $test->id, "save should save the model to the db and update the auto_increment column" );

        $test->delete();

    }

    public function test_get_dirty_columns () {

        $test = Test::fetch_one();

        $dirtyValue = $test->name . 'a';

        $test->name = $dirtyValue;

        $expected = ['name' => $dirtyValue];

        $this->assertEquals( $expected, $test->get_dirty_columns() );
    }

    public function test_populate () {

        $dataArray = [
            'name' => 'phpunit',
            'dateTimeAdded' => '2016-02-05 16:30:09',
        ];

        $test = new Test($dataArray);

        $this->assertEquals( $dataArray['name'], $test->name );
        $this->assertEquals( $dataArray['dateTimeAdded'], $test->dateTimeAdded );

        $dataArray = [
            'name'          => 'phpunit2',
            'dateTimeAdded' => '2016-02-05 16:30:10',
        ];

        $test->populate( $dataArray );

        $this->assertEquals( $dataArray['name'], $test->name );
        $this->assertEquals( $dataArray['dateTimeAdded'], $test->dateTimeAdded );
    }
    
    public function test_fetch_has_many () {
        
        
    }
}
