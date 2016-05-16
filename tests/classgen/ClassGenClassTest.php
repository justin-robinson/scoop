<?php

use Scoop\ClassGen\ClassGenClass;

/**
 * Class ClassGenClassTest
 */
class ClassGenClassTest extends PHPUnit_Framework_TestCase {

    private $class;

    public function __construct () {

        $this->class = new ClassGenClass( 'testClass' );
    }

    public function test_get_header () {

        $expected =
            "<?php

class testClass {
";
        $this->assertEquals( $expected, $this->class->get_header() );

        $this->class->set_namespace( 'testNamespace' );
        $expected =
            "<?php

namespace testNamespace;

class testClass {
";
        $this->assertEquals( $expected, $this->class->get_header(), "namespace should be in the class header" );

        $expected =
            "<?php

namespace testNamespace;

class testClass extends testBaseClass {
";
        $this->class->set_extends( 'testBaseClass' );
        $this->assertEquals( $expected, $this->class->get_header(), "class should extend another class" );

        $expected =
            "<?php

namespace testNamespace;

class testClass extends testBaseClass implements Interface1, Interface2, Interface3 {
";
        $this->class->set_implements( [ 'Interface1', 'Interface2', 'Interface3' ] );
        $this->assertEquals( $expected, $this->class->get_header(), "class should implement interfaces" );

        $expected =
            "<?php

namespace testNamespace;

/**
 * Class testClass
 */
class testClass extends testBaseClass implements Interface1, Interface2, Interface3 {
";
        $this->class->set_phpDoc(
            '/**
 * Class testClass
 */' );
        $this->assertEquals( $expected, $this->class->get_header(), "class should have a phpdoc block" );

        $expected =
            "<?php

namespace testNamespace;

use someOtherTestClass;
use someTestClass;

/**
 * Class testClass
 */
class testClass extends testBaseClass implements Interface1, Interface2, Interface3 {
";
        $this->class->set_use( [ 'someTestClass', 'someOtherTestClass' ] );
        $this->assertEquals( $expected, $this->class->get_header(), "class should use classes in alphabetical order
        " );
    }

}
