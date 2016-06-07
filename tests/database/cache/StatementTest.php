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

        $this->cache = new Statement(500);

        $dbConfig = \Scoop\Config::get_db_config();

        $this->cache->put(0, new mysqli_stmt(
            new mysqli(
                $dbConfig['host'],
                $dbConfig['user'],
                $dbConfig['password'],
                '',
                $dbConfig['port']
            ),
            '' ));
    }

    public function test_put () {

        $this->cache->put(2, null, "putting a non mysqli_stmt should fail");

        $this->assertFalse( $this->cache->exists( 2 ) );

        $this->cache->put(2, $this->cache->get(0));

        $this->assertTrue( $this->cache->exists( 2 ), "putting a mysql_stmt should work" );
    }

    public function test___destruct () {

        $statement = $this->cache->get(0);
        
        unset($this->cache);

        $this->expectException(Exception::class);

        $statement->sqlstate;

    }
}
