<?php

use Scoop\Database\Cache\Statement;

/**
 * Class StatementTest
 */
class StatementTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Statement
     */
    private $cache;

    public function __construct () {

        $this->cache = new Statement();

        $dbConfig = \Scoop\Config::get_db_config();

        $this->cache->set(0, new mysqli_stmt(
            new mysqli(
                $dbConfig['host'],
                $dbConfig['user'],
                $dbConfig['password'],
                '',
                $dbConfig['port']
            ),
            '' ));
    }

    public function test_exists () {

        $this->assertFalse( $this->cache->exists( 1 ), "cache should be empty" );

        $this->cache->set(1, $this->cache->get(0));

        $this->assertTrue( $this->cache->exists( 1 ), "cache should contain something at index 0");
    }

    public function test_get () {

        $this->assertInstanceOf( 'mysqli_stmt', $this->cache->get( 0 ), 'cache should only hold mysqli_stmt' );
    }

    public function test_set () {

        $this->cache->set(2, null, "setting a non mysqli_stmt should fail");

        $this->assertFalse( $this->cache->exists( 2 ) );

        $this->cache->set(2, $this->cache->get(0));

        $this->assertTrue( $this->cache->exists( 2 ), "setting a mysql_stmt should work" );
    }
}
