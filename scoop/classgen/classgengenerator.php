<?php

namespace Scoop\ClassGen;

/**
 * Generates and saves a php class to a given file name
 * Class ClassGenGenerator
 * @package Scoop\ClassGen
 */
class ClassGenGenerator {

    /**
     * @var string file indentation to be used
     */
    public static $indentation = '    ';

    /**
     * @var ClassGenClass
     */
    public $class;

    /**
     * @var array ClassGenProperty[]
     */
    public $constantPropertiesArray = [ ];

    /**
     * @var array ClassGenProperty[]
     */
    public $publicPropertiesArray = [ ];

    /**
     * @var array ClassGenProperty[]
     */
    public $staticPropertiesArray = [ ];

    /**
     * @var array ClassGenProperty[]
     */
    public $protectedPropertiesArray = [ ];

    /**
     * @var array ClassGenProperty[]
     */
    public $privatePropertiesArray = [ ];

    /**
     * @var array ClassGenFunction[]
     */
    public $functionsArray = [ ];

    /**
     * @var null|string
     */
    public $filepath;

    /**
     * ClassGenGenerator constructor.
     *
     * @param ClassGenClass $class
     * @param string|null   $filepath
     */
    public function __construct ( ClassGenClass $class, string $filepath = '' ) {

        $this->class = $class;
        $this->filepath = $filepath;

    }

    /**
     * @param ClassGenProperty $property
     */
    public function addProperty ( ClassGenProperty $property ) {

        // add property to the right array
        if ( $property->isStatic ) {
            $this->staticPropertiesArray[] = $property;
        } else if ( $property->is_const () ) {
            $this->constantPropertiesArray[] = $property;
        } else {
            switch ( $property->get_visibility () ) {
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

    /**
     * @param $function
     */
//    public function addFunction ( $function ) {
//
//        array_push ( $this->functionsArray, $function );
//    }

    /**
     * @throws \Exception
     */
    public function save () {

        // open php tag and declare class
        $fileContents = $this->class->getHeader ();

        /* START PROPERTY GENERATION */

        /**
         * @var $constantProperty ClassGenProperty
         */
        // generate constants
        foreach ( $this->constantPropertiesArray as $constantProperty ) {
            $fileContents .= $constantProperty->get ();
        }

        /**
         * @var $staticProperty ClassGenProperty
         */
        // generate static properties
        foreach ( $this->staticPropertiesArray as $staticProperty ) {

            $fileContents .= $staticProperty->get ();

        }

        $fileContents .= PHP_EOL;

        /**
         * @var $publicProperty ClassGenProperty
         */
        // generate public properties
        foreach ( $this->publicPropertiesArray as $publicProperty ) {

            $fileContents .= $publicProperty->get ();

        }

        $fileContents .= PHP_EOL;

        /**
         * @var $protectedProperty ClassGenProperty
         */
        // generate protected properties
        foreach ( $this->protectedPropertiesArray as $protectedProperty ) {

            $fileContents .= $protectedProperty->get ();

        }

        $fileContents .= PHP_EOL;

        /**
         * @var $privateProperty ClassGenProperty
         */
        // generate private properties
        foreach ( $this->privatePropertiesArray as $privateProperty ) {

            $fileContents .= $privateProperty->get ();

        }

        $fileContents .= PHP_EOL;

        // generate functions
//        foreach ( $this->functionsArray as $method ) {
//
//            $fileContents .= $method->get ();
//        }

        // close the class
        $fileContents .= $this->class->getFooter ();

        // ensure path to output file exists
        $this->createPath ();

        // save file and set permissions
        if ( file_put_contents ( $this->filepath, $fileContents ) ) {
            chmod ( $this->filepath, 0777 );
        }

    }

    /**
     * @throws \Exception
     */
    private function createPath () {

        // break file path up
        $dirname = pathinfo ( $this->filepath, PATHINFO_DIRNAME );

        // create directory if it doesn't exist
        if ( !( $created = file_exists ( $dirname ) ) ) {
            $created = mkdir ( $dirname, 0777, true );
        }

        if ( !$created ) {
            throw new \Exception( 'failed to create directory at ' . $dirname );
        }
    }


}
