<?php

use Scoop\Config;

/**
 * Class ConfigTest
 */
class ConfigTest extends PHPUnit_Framework_TestCase {

    public function test_get_db_config () {

        $expected = require __DIR__ . "/../configs/db.php";

        $this->assertEquals($expected, Config::get_db_config());
    }

    public function test_get_sites_folder () {

        $sitesFolder = Config::get_option('sites_folder');

        Config::set_option('sites_folder', '/tmp/sites');

        $this->assertEquals('/tmp/sites', Config::get_sites_folder());

        Config::set_option('sites_folder', $sitesFolder);
    }

    public function test_get_shared_class_path () {

        $sharedClassPathParentDirectory = Config::get_option('shared_classpath_parent_directory');
        
        Config::unset_option('shared_classpath_parent_directory');

        $this->assertNull(Config::get_shared_class_path(), 'shared class path should be null if the parent directory is undefined');

        Config::set_option('shared_classpath_parent_directory', $sharedClassPathParentDirectory);
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
