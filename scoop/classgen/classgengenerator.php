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
     * @var null|string
     */
    public $filepath;

    /**
     * @var array ClassGenFunction[]
     */
    public $functionsArray = [ ];

    /**
     * @var array ClassGenProperty[]
     */
    public $privatePropertiesArray = [ ];

    /**
     * @var array ClassGenProperty[]
     */
    public $protectedPropertiesArray = [ ];

    /**
     * @var array ClassGenProperty[]
     */
    public $publicPropertiesArray = [ ];

    /**
     * @var array ClassGenProperty[]
     */
    public $staticPropertiesArray = [ ];

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
    public function add_property ( ClassGenProperty $property ) {

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
     * @return string
     * @throws \Exception
     */
    public function get_file_contents () : string {
        $classBody = '';
        /* START PROPERTY GENERATION */
        /**
         * @var $constantProperty ClassGenProperty
         */
        // generate constants
        foreach ( $this->constantPropertiesArray as $constantProperty ) {
            $classBody .= $constantProperty->get ();
        }
        if ( !empty($this->constantPropertiesArray) ) {
            $classBody .= PHP_EOL;
        }
        /**
         * @var $staticProperty ClassGenProperty
         */
        // generate static properties
        foreach ( $this->staticPropertiesArray as $staticProperty ) {
            $classBody .= $staticProperty->get ();
        }
        if ( !empty($this->staticPropertiesArray) ) {
            $classBody .= PHP_EOL;
        }
        /**
         * @var $publicProperty ClassGenProperty
         */
        // generate public properties
        foreach ( $this->publicPropertiesArray as $publicProperty ) {
            $classBody .= $publicProperty->get ();
        }
        if ( !empty($this->publicPropertiesArray) ) {
            $classBody .= PHP_EOL;
        }
        /**
         * @var $protectedProperty ClassGenProperty
         */
        // generate protected properties
        foreach ( $this->protectedPropertiesArray as $protectedProperty ) {
            $classBody .= $protectedProperty->get ();
        }
        if ( !empty($this->protectedPropertiesArray) ) {
            $classBody .= PHP_EOL;
        }
        /**
         * @var $privateProperty ClassGenProperty
         */
        // generate private properties
        foreach ( $this->privatePropertiesArray as $privateProperty ) {
            $classBody .= $privateProperty->get ();
        }
        if ( !empty($this->privatePropertiesArray) ) {
            $classBody .= PHP_EOL;
        }
        $classBody = PHP_EOL . $classBody;
        return $this->class->get_header() . $classBody . $this->class->get_footer ();
    }

    /**
     * @throws \Exception
     */
    public function save () {

        // ensure path to output file exists
        $this->create_path ();

        // save file and set permissions
        if ( file_put_contents ( $this->filepath, $this->get_file_contents() ) ) {
            chmod ( $this->filepath, 0777 );
        }

    }

    /**
     * @throws \Exception
     */
    private function create_path () {

        // break file path up
        $dirname = dirname($this->filepath);

        // create directory if it doesn't exist
        if ( !( $created = file_exists ( $dirname ) ) ) {
            $created = mkdir ( $dirname, 0777, true );
        }

        if ( !$created ) {
            throw new \Exception( 'failed to create directory at ' . $dirname );
        }
    }


}
