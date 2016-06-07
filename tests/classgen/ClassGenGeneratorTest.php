<?php

use Scoop\ClassGen\ClassGenClass;
use Scoop\ClassGen\ClassGenGenerator;
use Scoop\ClassGen\ClassGenProperty;

/**
 * Class ClassGenClassTest
 */
class ClassGenGeneratorTest extends PHPUnit_Framework_TestCase {

    private $class;

    public function __construct () {

        $this->class = new ClassGenClass('test');
    }

    public function test_add_property () {

        $generator = new ClassGenGenerator( $this->class, '/tmp/testClass.php' );

        $property = new ClassGenProperty( 'p1' );
        $property->set_public();
        $generator->add_property( $property );

        $property = new ClassGenProperty( 'p2' );
        $property->set_protected();
        $generator->add_property( $property );

        $property = new ClassGenProperty( 'p3' );
        $property->set_private();
        $generator->add_property( $property );

        $property = new ClassGenProperty( 'p4' );
        $property->set_const();
        $generator->add_property( $property );

        $property = new ClassGenProperty( 'p5' );
        $property->set_static( true );
        $generator->add_property( $property );

        $fileContents = $generator->get_file_contents();

        $expectedContents =
'<?php

class test {

    const P4 = NULL;

    public static $p5 = NULL;

    public $p1 = NULL;

    protected $p2 = NULL;

    private $p3 = NULL;

}

?>';

        $this->assertEquals( $expectedContents, $fileContents, "class properties should be included and sorted correctly" );
    }

}
