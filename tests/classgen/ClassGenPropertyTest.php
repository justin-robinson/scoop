<?php

use Scoop\ClassGen\ClassGenGenerator;
use Scoop\ClassGen\ClassGenProperty;

/**
 * Class ClassGenPropertyTest
 */
class ClassGenPropertyTest extends PHPUnit_Framework_TestCase {

    public function test_get_header () {

        $property = new ClassGenProperty('name');

        $expected = ClassGenGenerator::$indentation . 'public $name = NULL;' . PHP_EOL;
        $this->assertEquals( $expected, $property->get_header(), "empty properties should be null");
        
        $property->set_value( 'value' );
        $expected = ClassGenGenerator::$indentation . 'public $name = \'value\';' . PHP_EOL;
        $this->assertEquals( $expected, $property->get_header(), "property values should be assigned");
        
        $property->set_static();
        $expected = ClassGenGenerator::$indentation . 'public static $name = \'value\';' . PHP_EOL;
        $this->assertEquals( $expected, $property->get_header(), "property should be static");

        $property->set_const();
        $expected = ClassGenGenerator::$indentation . 'const NAME = \'value\';' . PHP_EOL;
        $this->assertEquals( $expected, $property->get_header(), "constants should be uppercased");
    }
}
