<?php

use Scoop\Config;

/**
 * Class ConfigTest
 */
class ConfigTest extends PHPUnit_Framework_TestCase {

    public function test_get_db_config () {

        $expected = [
            'host'     => 'jor.pw',
            'user'     => 'scoop',
            'password' => '',
            'port'     => '3306',
        ];

        $this->assertEquals($expected, Config::get_db_config());
    }

    public function test_option_exists () {

        $value = rand(1,99999);
        $optionName = 'test_option_exists' . $value;

        $this->assertFalse(Config::option_exists($optionName), "{$optionName} shouldn't exist yet");

        Config::set_option($optionName, $value);

        $this->assertTrue(Config::option_exists($optionName), "{$optionName} should exist now");
        $returnedValue = Config::get_option($optionName);
        $this->assertEquals($value, $returnedValue, "{$optionName} should equal {$value}, got {$returnedValue}");

    }
    
    public function test_set_options () {
        
        $options = [
            'dummy1' => 'value1',
            'dummy2' => 'value2',
            'dummy3' => 'value3',
        ];
        
        Config::set_options($options);
        
        $this->assertEquals('value1', Config::get_option('dummy1'));
        $this->assertEquals('value2', Config::get_option('dummy2'));
        $this->assertEquals('value3', Config::get_option('dummy3'));
    }
}
