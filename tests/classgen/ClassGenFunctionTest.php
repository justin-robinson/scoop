<?php

class ClassGenFunctionTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \Scoop\ClassGen\ClassGenFunction
     */
    public $function;

    public function setUp() {
        $this->function = new \Scoop\ClassGen\ClassGenFunction('testFunction', '$one = 1, $two = 2', 'echo $one + $two;');
    }

    public function test_get_header () {

        $expected =
            'public function testFunction ($one = 1, $two = 2) {
    echo $one + $two;
}';

        $this->assertEquals($expected, $this->function->get());

        $this->function->set_final();

        $expected =
            'final public function testFunction ($one = 1, $two = 2) {
    echo $one + $two;
}';
        $this->assertEquals($expected, $this->function->get());
    }

    public function test_final_abstract_function () {
        $this->function->set_final();
        $this->function->set_abstract();

        $this->expectException(\Exception::class);
        $this->function->get();
    }

    public function test_private_abstract_function () {
        $this->function->set_private();
        $this->function->set_abstract();

        $this->expectException(\Exception::class);
        $this->function->get();
    }

    public function test_abstract_function () {

        $body = $this->function->body;

        $this->function->body = '';
        $this->function->set_abstract();

        $expected = 'abstract public function testFunction ($one = 1, $two = 2);';
        $this->assertEquals($expected, $this->function->get());

        $this->expectException(\Exception::class);
        $this->function->body = $body;
        $this->function->get();
    }

    public function test_default_body () {
        $this->function = new \Scoop\ClassGen\ClassGenFunction('testFunction', '$one = 1, $two = 2');

        $expected = 'public function testFunction ($one = 1, $two = 2) {
    // TODO: Implement testFunction() function.
}';

        $this->assertEquals($expected, $this->function->get());
    }
}
