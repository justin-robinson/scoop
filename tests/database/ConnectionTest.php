<?php

use Scoop\Database\Connection;

/**
 * Class ConnectionTest
 */
class ConnectionTest extends PHPUnit_Framework_TestCase {

    public function test_execute_select () {

        $sql = "
            SELECT
                *
            FROM
              `jor.pw`.`test`
            WHERE
              `id` = ?";

        $queryParams = [1];

        $result = Connection::execute( $sql, $queryParams );

        $this->assertEquals(1, $result->num_rows, "select should return a row");
    }

    public function test_execute_insert () {

        $sql = "
            INSERT INTO
              `jor.pw`.`test`
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
              `jor.pw`.`test`
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
}
