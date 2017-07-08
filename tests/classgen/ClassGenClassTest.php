<?php

use Scoop\ClassGen\ClassGenClass;

/**
 * Class ClassGenClassTest
 */
class ClassGenClassTest extends \PHPUnit\Framework\TestCase {

    public function test___construct () {

        $class = new ClassGenClass( 'testClass', 'otherTestClass', 'test\Class\Namespace', ['Iterator'] );

        $this->assertEquals('testClass', $class->name);
        $this->assertEquals('otherTestClass', $class->extends);
        $this->assertEquals('test\Class\Namespace', $class->namespace);
        $this->assertEquals(['Iterator'], $class->implements);
    }

    public function test_append_use () {

        $class  = new ClassGenClass('testClass');

        $class->append_use('otherTestClass');

        $this->assertEquals(['otherTestClass'], $class->use);
    }

    public function test_get_header () {

        $class = new ClassGenClass( 'testClass' );

        $expected =
            "<?php

class testClass {
";
        $this->assertEquals( $expected, $class->get_header() );

        $class->set_namespace( 'testNamespace' );
        $expected =
            "<?php

namespace testNamespace;

class testClass {
";
        $this->assertEquals( $expected, $class->get_header(), "namespace should be in the class header" );

        $expected =
            "<?php

namespace testNamespace;

class testClass extends testBaseClass {
";
        $class->set_extends( 'testBaseClass' );
        $this->assertEquals( $expected, $class->get_header(), "class should extend another class" );

        $expected =
            "<?php

namespace testNamespace;

class testClass extends testBaseClass implements Interface1, Interface2, Interface3 {
";
        $class->set_implements( [ 'Interface1', 'Interface2', 'Interface3' ] );
        $this->assertEquals( $expected, $class->get_header(), "class should implement interfaces" );

        $expected =
            "<?php

namespace testNamespace;

/**
 * Class testClass
 */
class testClass extends testBaseClass implements Interface1, Interface2, Interface3 {
";
        $class->set_phpDoc(
            '/**
 * Class testClass
 */' );
        $this->assertEquals( $expected, $class->get_header(), "class should have a phpdoc block" );

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
        $class->set_use( [ 'someTestClass', 'someOtherTestClass' ] );
        $this->assertEquals( $expected, $class->get_header(), "class should use classes in alphabetical order" );

        $expected =
            "<?php

namespace testNamespace;

use someOtherTestClass;
use someTestClass;

/**
 * Class testClass
 */
abstract class testClass extends testBaseClass implements Interface1, Interface2, Interface3 {
";
        $class->set_abstract();
        $this->assertEquals( $expected, $class->get_header(), "class should be abstract" );

        $expected =
            "<?php

namespace testNamespace;

use someOtherTestClass;
use someTestClass;

/**
 * Class testClass
 */
final class testClass extends testBaseClass implements Interface1, Interface2, Interface3 {
";
        $class->set_abstract(false);
        $class->set_final();
        $this->assertEquals( $expected, $class->get_header(), "class should be final" );

        $this->expectException( \Exception::class );
        $class->set_abstract();
        $class->get_header();

    }

}
