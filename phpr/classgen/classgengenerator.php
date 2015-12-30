<?

/*
 * Generates and saves a php class to a given file name
 */
namespace phpr\ClassGen;

class ClassGenGenerator {

    // file indentation to be used
    public static $indentation = '    ';

    public $class;
    public $constantPropertiesArray = [];
    public $publicPropertiesArray = [];
    public $staticPropertiesArray = [];
    public $protectedPropertiesArray = [];
    public $privatePropertiesArray = [];
    public $functionsArray = [];
    public $filepath;

    public function __construct ( ClassGenClass $class, $filepath = null ) {

        $this->class = $class;
        $this->filepath = $filepath;

    }

    public function addProperty ( ClassGenProperty $property ) {

        // add property to the right array
        if ( $property->isStatic ) {
            $this->staticPropertiesArray[] = $property;
        } else if ( $property->is_const() ) {
            $this->constantPropertiesArray[] = $property;
        } else {
            switch( $property->get_visibility() ) {
                case 'public' :
                    $this->publicPropertiesArray[] = $property;
                    break;
                case 'private' :
                    $this->privatePropertiesArray[] = $property;
                    break;
                case 'protected' :
                    $this->protectedPropertiesArray[] = $property;
                    break;
                default:
                    break;
            }
        }
    }

    public function addFunction ( $function ) {
        array_push($this->functionsArray, $function);
    }


    // write class to file
    public function save () {

        // open php tag and declare class
        $fileContents = $this->class->getHeader();

        /* START PROPERTY GENERATION */

        // generate constants
        foreach ( $this->constantPropertiesArray as $constantProperty ) {
            $fileContents .= $constantProperty->get();
        }

        // generate static properties
        foreach ( $this->staticPropertiesArray as $staticProperty ) {

            $fileContents .= $staticProperty->get();

        }

        $fileContents .= PHP_EOL;

        // generate public properties
        foreach ( $this->publicPropertiesArray as $publicProperty ) {

            $fileContents .= $publicProperty->get();

        }

        $fileContents .= PHP_EOL;

        // generate protected properties
        foreach ( $this->protectedPropertiesArray as $protectedProperty ) {

            $fileContents .= $protectedProperty->get();

        }

        $fileContents .= PHP_EOL;

        // generate private properties
        foreach ( $this->privatePropertiesArray as $privateProperty ) {

            $fileContents .= $privateProperty->get();

        }

        $fileContents .= PHP_EOL;

        // generate functions
        foreach ( $this->functionsArray as $method ) {

            $fileContents .= $method->get();
        }

        // close the class
        $fileContents .= $this->class->getFooter();

        // ensure path to output file exists
        $this->createPath();

        // save file and set permissions
        if ( file_put_contents($this->filepath, $fileContents) ) {
            chmod($this->filepath, 0777);
        }

    }

    private function createPath () {

        // break file path up
        $pathParts = (object)pathinfo($this->filepath);

        // check that final directory exists
        $created = file_exists($pathParts->dirname);

        // create directory if it doesn't exist
        if ( !$created ) {
            $created = mkdir($pathParts->dirname, 0777, true);
        }

        if ( !$created ) {
            throw new Error('failed to create directory at ' . $pathParts->dirname);
        }
    }


}