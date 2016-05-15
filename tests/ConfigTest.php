<?php

use Scoop\Config;

/**
 * Class ConfigTest
 */
class ConfigTest extends PHPUnit_Framework_TestCase {

    public function test_option_exists () {

        $value = rand(1,99999);
        $optionName = 'test_option_exists' . $value;

        $this->assertFalse(Config::option_exists($optionName), "{$optionName} shouldn't exist yet");

        Config::set_option($optionName, $value);

        $this->assertTrue(Config::option_exists($optionName), "{$optionName} should exist now");
        $returnedValue = Config::get_option($optionName);
        $this->assertEquals($value, $returnedValue, "{$optionName} should equal {$value}, got {$returnedValue}");

    }
}
