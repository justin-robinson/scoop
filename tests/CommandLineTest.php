<?php

use Scoop\CommandLine;

/**
 * Class CommandLineTest
 */
class CommandLineTest extends PHPUnit_Framework_TestCase {

    public function testParseArgs () {

        $argv = [
            'some-file-name.php',
            'one',
            'two',
            '-o',
            'short-opt-value',
            '--long-opt',
            'long-opt-value',
            '--long=equals'
        ];

        $expected = [
            'one',
            'two',
            'o' => 'short-opt-value',
            'long-opt' => 'long-opt-value',
            'long' => 'equals'
        ];

        $this->assertEquals($expected, CommandLine::parseArgs($argv));
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

        CommandLine::parseArgs($argv);

        $this->assertTrue( CommandLine::getBoolean( 's' ), "cli arg value 'y' should be returned as true");
        $this->assertFalse( CommandLine::getBoolean( 'b' ), "cli arg value 'false' should be returned as false" );
        $this->assertTrue( CommandLine::getBoolean( 'i' ), "cli arg value '1' should be returned as true");
        $this->assertTrue( CommandLine::getBoolean( 'B' ), "cli arg value 'B' should be returned as true");
        $this->assertFalse( CommandLine::getBoolean( 'dne' ), "cli arg value 'dne' should be returned as false");
        $this->assertEquals('default', CommandLine::getBoolean( 'a', 'default' ), "cli arg value 'a' should be return the default value");

    }
}
