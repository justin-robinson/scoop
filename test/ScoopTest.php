<?php

/**
 * Class ScoopTest
 */
class ScoopTest extends PHPUnit_Framework_TestCase {

    public function testDBRead() {
        $test = \DB\JorPw\Test::fetch_by_id(1);
        $this->assertEquals(1, $test->id);
    }

    public function testDBWrite() {
        $test = new \DB\JorPw\Test();

        $test->name = time();

        $test->save();

        $id = $test->id;

        $test->delete();

        $this->assertNotEmpty($id);
    }

    public function testDBDelete() {
        $test = new \DB\JorPw\Test();

        $test->name = time();

        $test->save();

        $test->delete();

        $this->assertFalse($test->is_loaded_from_database());
    }

    public function testDBUpdate() {
        $test = \DB\JorPw\Test::fetch_by_id(1);
        $newValue = (string)((int)$test->name + 1);
        $test->name = $newValue;
        $test->save();
        $test = \DB\JorPw\Test::fetch_by_id(1);

        $this->assertEquals($test->name, $newValue);
    }
}
