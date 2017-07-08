<?php

use Scoop\CommandLine;

/**
 * Class CommandLineTest
 */
class CommandLineTest extends \PHPUnit\Framework\TestCase {

    public function testParseArgs () {

        $argv = [
            'some-file-name.php',
            'one',
            'two',
            '-o',
            'short-opt-value',
            '--long-opt',
            'long-opt-value',
            '--long=equals',
            '-s=equals',
            '--foo',
            'bar'
        ];

        $expected = [
            'one',
            'two',
            'o' => 'short-opt-value',
            'long-opt' => 'long-opt-value',
            'long' => 'equals',
            's' => 'equals',
            'foo' => 'bar'
        ];

        $this->assertEquals($expected, CommandLine::parse_args( $argv));
    }

    public function testGetBoolean () {

        $argv = [
            'some-file-name.php',
            '-s',
            'y',
            '-b',
            'false',
            '-i',
            1,
            '-B',
            true,
            '-a',
            'pop'
        ];

        CommandLine::parse_args( $argv);

        $this->assertTrue( CommandLine::get_boolean( 's' ), "cli arg value 'y' should be returned as true");
        $this->assertFalse( CommandLine::get_boolean( 'b' ), "cli arg value 'false' should be returned as false" );
        $this->assertTrue( CommandLine::get_boolean( 'i' ), "cli arg value '1' should be returned as true");
        $this->assertTrue( CommandLine::get_boolean( 'B' ), "cli arg value 'B' should be returned as true");
        $this->assertFalse( CommandLine::get_boolean( 'dne' ), "cli arg value 'dne' should be returned as false");
        $this->assertEquals( 'default', CommandLine::get_boolean( 'a', 'default' ), "cli arg value 'a' should be return the default value");

    }
}
