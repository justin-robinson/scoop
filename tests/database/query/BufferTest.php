<?php

use DB\Scoop\Test;
use Scoop\Database\Query\Buffer;

/**
 * Class BufferTest
 */
class BufferTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Buffer
     */
    private $buffer;

    /**
     * @var int
     */
    private $maxSize = 10;

    /**
     * @var Test[]
     */
    private $testModels;

    public function __construct () {

        $this->buffer = new Buffer($this->maxSize, Test::class);

        $this->testModels = [];

        foreach ( range(0,$this->maxSize-1) as $_) {
            $this->testModels[] = new Test(
                [
                    'name' => 'inserted from phpunit',
                    'dateTimeAdded' => new Scoop\Database\Literal( 'NOW()' ),
                ] );
        }
    }

    public function test___construct () {

        $this->expectException( Exception::class );
        new Buffer($this->maxSize, stdClass::class, "buffer should throw an error is the model class isn't a scoop model");

        $buffer = new Buffer($this->maxSize, Test::class);

        $this->assertInstanceOf(Buffer::class, $buffer);
    }

    public function test_insert () {

        foreach ( range(0, $this->maxSize-1) as $i) {
            $this->buffer->insert( $this->testModels[$i] );
        }

        $this->buffer->insert( $this->testModels[0] );

        foreach ( $this->testModels as $test ) {
            $this->assertNotEmpty( $test->id, "inserting more than rows than the max size should trigger a flush" );
            $test->delete();
        }

        $buffer = new Buffer(0, Test::class);
        $test = new Test();
        $this->assertFalse($buffer->insert($test), "inserting to a zero sized buffer should fail");
    }

    public function test_flush () {

        $test = new Test(
            [
                'name' => 'inserted from phpunit',
                'dateTimeAdded' => new Scoop\Database\Literal( 'NOW()' ),
            ]
        );

        $this->buffer->insert( $test );

        $this->buffer->flush();

        $this->assertNotEmpty( $test->id, "flushind the buffer should save to db and update auto increment column on model" );

        $test->delete();

        $buffer = new Buffer(0, Test::class);
        $this->assertFalse($buffer->flush(), "flushing a zero sized buffer should fail");
    }

    public function test_on_flush_event () {

        $test = new Test(
            [
                'name' => 'inserted from phpunit',
                'dateTimeAdded' => new \Scoop\Database\Literal('NOW()'),
            ]
        );

        $onFlush = function () use ( $test ) {
            $test->dummy = 1;
        };

        $this->buffer->on_flush($onFlush);

        $this->buffer->insert($test);

        $this->buffer->flush();

        $this->assertEquals(1, $test->dummy, "on flush callback should be called");

        $test->delete();
    }

    public function test_set_insert_ignore () {

        $this->buffer->set_insert_ignore(true);
        $this->assertTrue($this->buffer->get_insert_ignore());

        $this->buffer->set_insert_ignore(1);
        $this->assertTrue($this->buffer->get_insert_ignore());

        $this->buffer->set_insert_ignore(0);
        $this->assertFalse($this->buffer->get_insert_ignore());
    }

}
