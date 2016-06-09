<?php

use Scoop\Config;

/**
 * Class ConfigTest
 */
class ConfigTest extends PHPUnit_Framework_TestCase {

    /**
     * ([a-z]:)?                        optional windows C: drive stuff
     * [\/\\\]                          required / or \
     * [\w -!@#%&<>\$\^\*\(\)\+\|\.]*   optional folder name with special characters allowed
     * [\/\\\]                          required trailing / or \ on folder name
     */
    const REGEX_FILE_PATH = '/^([a-z]:)?[\/\\\\]([\w -!@#%&<>\$\^\*\(\)\+\|\.]*[\/\\\\])*$/i';

    public function test_get_class_paths () {

        foreach ( Config::get_class_paths() as $classPath ) {

            $this->assertEquals(1, preg_match(self::REGEX_FILE_PATH, $classPath), "classpaths should be a valid file path");
        }
    }

    public function test_get_db_config () {

        // base db config
        $expected = require __DIR__ . "/../configs/db.php";
        $this->assertEquals($expected, Config::get_db_config());

        // site db config
        if ( Config::option_exists('server_document_root') ) {
            $docRoot = Config::get_option('server_document_root');
        }

        // set up a fake site document root
        Config::set_option('server_document_root', getcwd() . '/' . time());

        // get the expected config file path
        $siteDbConfigPath = Config::get_site_db_config_path();

        // create the expected config file path
        if ( mkdir(Config::get_site_class_path(), 0777, true) && mkdir(dirname($siteDbConfigPath), 0777, true) ) {

            // the site db config values
            $siteDbConfig = [
                'host' => 'new.host',
                'user' => 'new.user'
            ];

            // create the php code for the site config file
            $contents = '<?php return ' . var_export($siteDbConfig, true) . ';';

            // write the config to file
            $bytesWritten = file_put_contents($siteDbConfigPath, $contents, true);

            if ( $bytesWritten !== false ) {

                // reload global db config in hopes site values have overridden global ones
                $dbConfig = Config::get_db_config();

                // check that site db config values are there
                foreach ( $siteDbConfig as $key => $value ) {
                    $this->assertEquals($value, $dbConfig[$key], "options in site db config should override the global db config");
                }

                // check values not in site db config are still loaded from global config
                foreach ( $expected as $key => $value ) {
                    if ( !array_key_exists($key, $siteDbConfig) ) {
                        $this->assertEquals($value, $dbConfig[$key], "options not in site db config should be loaded from the global config");
                    }
                }
            }
        }

        // delete our tmp site doc root
        \Scoop\File::delete_directory(Config::get_option('server_document_root'));

        // set config values back to what they were
        if ( isset($docRoot) ) {
            Config::set_option('server_document_root', $docRoot);
        } else {
            Config::unset_option('server_document_root');
        }
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

        $this->assertEmpty(Config::get_shared_class_path(), 'shared class path should be empty if the parent directory is undefined');

        Config::set_option('shared_classpath_parent_directory', $sharedClassPathParentDirectory);
    }

    public function test_get_site_class_path_by_name () {

        $siteName = 'jor.pw';

        $classPath = Config::get_site_class_path_by_name($siteName);

        $this->assertEquals(1, preg_match(self::REGEX_FILE_PATH, $classPath), "site classpath should be a valid file path");

        $this->assertNotFalse(strpos($classPath, $siteName), "site classpath should contain the sitename");
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
