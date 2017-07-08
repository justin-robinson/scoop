<?php

use Scoop\Database\Model\Generic;

/**
 * Class GenericTest
 */
class GenericTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var Generics
     */
    private $generic;

    /**
     * @var array
     */
    private $genericData = [
        'one' => 1,
        'two' => 2,
    ];

    public function __construct () {

        $this->generic = new Generic($this->genericData);

        parent::__construct();
    }

    public function test_query () {

        $limit = 5;

        $rows = Generic::query( "SELECT * FROM `scoop`.`test` LIMIT ?", [ $limit ] );

        $this->assertEquals( $limit, $rows->get_num_rows() );
        
        $dbConfig = \Scoop\Config::get_db_config();
        $dbConfig['database'] = 'scoop';
        $connection = new \Scoop\Database\Connection($dbConfig);

        $rows = Generic::query( "SELECT * FROM test LIMIT ?", [ $limit ], $connection);

        $this->assertEquals($limit, $rows->get_num_rows());

    }

    public function test___get () {

        $this->assertEquals( 1, $this->generic->one, "__get should return the value for a column" );
        $this->assertNull( $this->generic->three, "_get should return null if a column does not exist" );
    }

    public function test___isset () {

        $this->assertTrue(isset($this->generic->one), "isset should be true if a column exists");
        $this->assertFalse(isset($this->generic->three), "isset shoudl be false if a column does not exist");
    }

    public function test___set () {

        $this->generic->one = 11;

        $this->assertEquals(11, $this->generic->one, "__set should set a db value");

        $this->generic->one = 1;
    }

    public function test___toString () {

        $expected = [
            'id' => null,
            'name' => 'something',
            'dateTimeAdded' => '0'
        ];

        $test = new \DB\Scoop\Test($expected);

        $this->assertEquals(var_export($expected, true), (string)$test, "generics should be castable to strings as an array of their data");
    }

    public function test_get_column_names () {

        $expectedColumnNames = ['one', 'two'];

        foreach ( $this->generic->get_column_names() as $i => $columnName ) {
            $this->assertEquals( $expectedColumnNames[$i], $columnName );
        }
    }

    public function test_is_loaded_from_db () {

        $generic = new Generic();

        $this->assertFalse($generic->get_loaded_from_database(), "created models should not be marked loaded from the database");

        $this->assertTrue(\DB\Scoop\Test::fetch_one()->get_loaded_from_database(), "fetched models should be marked as loaded from the database");
    }

    public function test_populate () {

        $this->generic->populate( ['one' => 2, 'three' => 3] );
        
        $this->assertEquals( 2, $this->generic->one, "populate should override existing properties" );
        $this->assertEquals( 2, $this->generic->two, "populate should not erase old properties");
        $this->assertEquals( 3, $this->generic->three, "populate should add new properties" );

        $this->generic = new Generic($this->genericData);
    }

    public function test_to_array () {

        $dbValues = $this->generic->get_db_values_array();

        foreach ( $dbValues as $i => $v ) {
            $this->assertEquals( $this->genericData[$i], $v );
        }

        foreach ( $this->genericData as $i => $v ) {
            $this->assertEquals( $dbValues[$i], $v );
        }
    }

    public function test_jsonSerialize () {

        $this->assertEquals( json_encode($this->genericData), json_encode($this->generic), "json encode should encode the db values" );
    }


}
