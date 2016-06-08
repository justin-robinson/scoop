<?php

use Scoop\Database\Connection;

/**
 * Class ConnectionTest
 */
class ConnectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Connection::__construct
     * @throws Exception
     */
    public function test_connect () {

        Connection::connect();

        $this->assertEquals(true, Connection::is_connected(), "connection should exist after calling connect");
    }

    public function test_execute_select () {

        $sql = "
            SELECT
                *
            FROM
              `scoop`.`test`
            WHERE
              `id` = ?";

        $queryParams = [1];

        $result = Connection::execute( $sql, $queryParams );

        $this->assertEquals(1, $result->num_rows, "select should return a row");
    }

    public function test_execute_insert () {

        $sql = "
            INSERT INTO
              `scoop`.`test`
            (`name`)
            VALUES
            (?)";

        $queryParams = ['inserted from phpunit'];

        Connection::execute( $sql, $queryParams );

        $id = Connection::get_insert_id();
        $affectedRows = Connection::get_affected_rows();

        $this->assertNotEmpty( $id, "db auto_increment id should be saved upon insert" );
        $this->assertEquals( 1, $affectedRows, "only one row should have been inserted");

        if ( $id ) {
            $sql = "
            DELETE FROM
              `scoop`.`test`
            WHERE
              `id` = ?";

            $queryParams = [$id];

            Connection::execute( $sql, $queryParams );
        }
    }

    public function test_get_bind_type () {

        $this->assertEquals( 's', Connection::get_bind_type( null ), "null should be bound as a string");
        $this->assertEquals( 's', Connection::get_bind_type( 'string' ) );
        $this->assertEquals( 's', Connection::get_bind_type( new Scoop\Database\Literal( 'NOW()' ) ) );
        $this->assertEquals( 'i', Connection::get_bind_type( 1 ) );
        $this->assertEquals( 'i', Connection::get_bind_type( true ) );
        $this->assertEquals( 'd', Connection::get_bind_type( 1.1 ) );

        $this->expectException( Exception::class );
        Connection::get_bind_type( new stdClass() );

    }

    public function test_get_statement_from_sql () {

        $this->expectException(Exception::class);

        \Scoop\Database\Model\Generic::query(
            "SELECT
                *
            FROM
              `something`.`that_doesnt_exist`"
        );
    }

    public function test_get_sql_history () {

        $this->assertEquals([], Connection::get_sql_history(), "sql history should be empty if logging is disabled");

        Connection::set_logging_enabled(true);

        \DB\Scoop\Test::fetch_by_id(1);

        $this->assertNotEmpty(Connection::get_sql_history(), "sql history should not be empty after enabling logging and running a query");

        Connection::set_logging_enabled(false);
    }
}
