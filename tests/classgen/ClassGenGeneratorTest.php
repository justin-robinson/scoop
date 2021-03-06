<?php

use Scoop\ClassGen\ClassGenClass;
use Scoop\ClassGen\ClassGenGenerator;
use Scoop\ClassGen\ClassGenProperty;

/**
 * Class ClassGenClassTest
 */
class ClassGenGeneratorTest extends \PHPUnit\Framework\TestCase {

    private $class;

    public function setUp () {

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

        $property = new ClassGenProperty( 'p6' );
        $property->visibility = 'super private';
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

    public function test_add_function () {


        $generator = new ClassGenGenerator( $this->class, '/tmp/testClass.php' );

        $property = new ClassGenProperty( 'p1' );
        $property->set_public();
        $generator->add_property( $property );

        $function = new \Scoop\ClassGen\ClassGenFunction('testFunction', '', '$one = 1;');
        $generator->add_function($function);

        $function = new \Scoop\ClassGen\ClassGenFunction('testFunction2', '', '$two = 2;');
        $generator->add_function($function);

        $expected = '<?php

class test {

    public $p1 = NULL;

    public function testFunction () {
        $one = 1;
    }

    public function testFunction2 () {
        $two = 2;
    }

}

?>';

        $this->assertEquals($expected, $generator->get_file_contents());
    }

    public function test_save () {

        $filePath = '/tmp/scoopTests/testClass.php';
        $dirname = dirname($filePath);

        $generator = new ClassGenGenerator( $this->class, $filePath);

        \Scoop\File::delete_directory($dirname);

        $generator->save();

        $this->assertFileExists($filePath, "saving a class generator should write to the file system");

        \Scoop\File::delete_directory($dirname);

    }

}
