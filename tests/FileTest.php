<?php

use Scoop\File;

/**
 * Class FileTest
 */
class FileTest extends PHPUnit_Framework_TestCase {

    public function test_create_path () {

        $path = getcwd() . DIRECTORY_SEPARATOR . microtime();

        $this->assertTrue(File::create_path($path));

        File::delete_directory($path);
    }

    public function test_delete_directory () {

        $fileName = getcwd() . DIRECTORY_SEPARATOR . microtime();

        // delete a file that doesn't exist
        $this->assertTrue(File::delete_directory($fileName), "deleting a file that doesn't exist should return true");

        // delete a file that does exist
        if ( file_put_contents($fileName, 'something') !== false ) {
            $this->assertTrue(File::delete_directory($fileName), "deleting a file that exist should return true");
            $this->assertFalse(file_exists($fileName), "file should not exist after deleting");
        }

        if ( mkdir($fileName, 0777, true) ) {

            foreach ( range(0,2) as $i ) {
                file_put_contents($fileName . DIRECTORY_SEPARATOR . $i, $i);
            }

            $this->assertTrue(File::delete_directory($fileName), "deleting a directory should return true");
            $this->assertFalse(file_exists($fileName), "directory should not exist after deleting");
        }
    }
}
