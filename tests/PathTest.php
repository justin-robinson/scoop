<?php

use Scoop\Path;

/**
 * Class PathTest
 */
class PathTest extends PHPUnit_Framework_TestCase {

    public function test_is_absolute () {

        $this->assertTrue(Path::is_absolute('/root'), "/root should be detected as an absolute path");
        $this->assertFalse(Path::is_absolute('root'), "root should NOT be detected as an absoute path");
    }

    public function test_make_absolute () {

        $this->assertTrue(Path::is_absolute(Path::make_absolute('root')));
    }
}
