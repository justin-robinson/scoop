<?php

use Scoop\Database\Connection;
use Scoop\Database\Model\Generic;

/**
 * Class ConnectionTest
 */
class ConnectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Connection
     */
    public $connection;

    /**
     * ConnectionTest constructor.
     */
    public function __construct () {

        $this->connection = new Connection(\Scoop\Config::get_db_config());
    }

    public function test___construct () {

        $dbConfig = \Scoop\Config::get_db_config();

        $connection = new Connection($dbConfig);

        $this->assertInstanceOf(Connection::class, $connection);

        $dbConfig['password'] = 'wrong';
        $this->expectException(\mysqli_sql_exception::class);
        new Connection($dbConfig);
    }

    public function test___destruct () {

        $connection = new Connection(\Scoop\Config::get_db_config());

        unset($connection);
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

        $result = $this->connection->execute( $sql, $queryParams );

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

        $this->connection->execute( $sql, $queryParams );

        $id = $this->connection->get_insert_id();
        $affectedRows = $this->connection->get_affected_rows();

        $this->assertNotEmpty( $id, "db auto_increment id should be saved upon insert" );
        $this->assertEquals( 1, $affectedRows, "only one row should have been inserted");

        if ( $id ) {
            $sql = "
            DELETE FROM
              `scoop`.`test`
            WHERE
              `id` = ?";

            $queryParams = [$id];

            $this->connection->execute( $sql, $queryParams );
        }
    }

    public function test_execute_failure () {

        $test = \DB\Scoop\Test::fetch_one();

        $this->expectException(Exception::class);
        \DB\Scoop\Test::query(
            "INSERT INTO
              scoop.test
             (id)
             VALUES
             (?, ?)",
            [$test->id]
        );
    }

    public function test_get_bind_type () {

        $this->assertEquals( 's', $this->connection->get_bind_type( null ), "null should be bound as a string");
        $this->assertEquals( 's', $this->connection->get_bind_type( 'string' ) );
        $this->assertEquals( 's', $this->connection->get_bind_type( new Scoop\Database\Literal( 'NOW()' ) ) );
        $this->assertEquals( 'i', $this->connection->get_bind_type( 1 ) );
        $this->assertEquals( 'i', $this->connection->get_bind_type( true ) );
        $this->assertEquals( 'd', $this->connection->get_bind_type( 1.1 ) );

        $this->expectException( Exception::class );
        $this->connection->get_bind_type( new stdClass() );

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

        $this->assertEquals([], $this->connection->get_sql_history(), "sql history should be empty if logging is disabled");

        Generic::$connection->set_logging_enabled(true);

        \DB\Scoop\Test::fetch_by_id(1);

        $this->assertNotEmpty(Generic::$connection->get_sql_history(), "sql history should not be empty after enabling logging and running a query");

        Generic::$connection->set_logging_enabled(false);
    }
}
