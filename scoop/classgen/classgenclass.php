<?php

namespace Scoop\ClassGen;

/**
 * Class ClassGenClass
 * @package Scoop\ClassGen
 */
class ClassGenClass extends ClassGenAbstract {

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $extends;

    /**
     * @var array
     */
    public $implements;

    /**
     * @var string
     */
    public $phpDoc = '';

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var array
     */
    public $use = [ ];

    /**
     * ClassGenClass constructor.
     *
     * @param        $name
     * @param null   $extends
     * @param string $namespace
     * @param array  $implements
     */
    public function __construct ( $name, $extends = null, $namespace = '', $implements = [ ] ) {

        $this->name = $name;
        $this->extends = $extends;
        $this->namespace = $namespace;
        $this->implements = $implements;

        return $this;
    }

    /**
     * @param $use
     *
     * @return $this
     */
    public function append_use ( $use ) {

        $this->use[] = $use;

        return $this;
    }

    /**
     * @param $name string
     *
     * @return $this
     */
    public function set_name ( string $name ) {

        $this->name = $name;

        return $this;
    }

    /**
     * @param $extends string
     *
     * @return $this
     */
    public function set_extends ( string $extends ) {

        $this->extends = $extends;

        return $this;
    }

    /**
     * @param array $implements
     *
     * @return $this
     */
    public function set_implements ( array $implements ) {

        $this->implements = $implements;

        return $this;
    }

    /**
     * @param $namespace string
     *
     * @return $this
     */
    public function set_namespace ( string $namespace ) {

        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param array $use
     *
     * @return $this
     */
    public function set_use ( array $use ) {

        $this->use = $use;

        return $this;
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function get_header () : string {

        if( $this->is_final() && $this->is_abstract() ) {
            throw new \Exception( 'Class can\'t be final and abstract' );
        }

        $classModifierArray = [ ];

        if( $this->is_final() ) {
            $classModifierArray[] = $this->modifierStrings[self::MODIFIER_FINAL];
        }

        if( $this->is_abstract() ) {
            $classModifierArray[] = $this->modifierStrings[self::MODIFIER_ABSTRACT];
        }

        $classModifier = implode( ' ', $classModifierArray );
        if( !empty($classModifier) ) {
            $classModifier .= ' ';
        }

        // class name and phpdoc
        $header = "<?php" . PHP_EOL . PHP_EOL;

        $header .= $this->get_namespace();

        $header .= $this->get_use();

        $header .= $this->get_phpDoc();

        $header .= $classModifier . "class {$this->name} ";

        // class extends
        $header .= $this->get_extends_code();

        // class implements
        $header .= $this->get_implements_code();

        $header .= " {" . PHP_EOL;

        return $header;
    }

    /**
     * @return string
     */
    private function get_extends_code () {

        if( empty($this->extends) ) {
            $code = '';
        } else {
            $code = 'extends ' . $this->extends . ' ';
        }

        return $code;
    }

    /**
     * @return string
     */
    private function get_implements_code () {

        if( empty($this->implements) ) {
            $code = '';
        } else {
            $code = 'implements ' . implode( ',', $this->implements );
        }

        return $code;
    }

    /**
     * @return string
     */
    public function get_footer () : string {

        $footer = '}' . PHP_EOL . PHP_EOL . '?>';

        return $footer;
    }

    /**
     * @param string $phpDoc
     */
    public function set_phpDoc ( string $phpDoc ) {

        $this->phpDoc = $phpDoc;
    }

    /**
     * @return string
     */
    private function get_namespace () : string {

        if( !empty($this->namespace) ) {
            return "namespace {$this->namespace};" . PHP_EOL . PHP_EOL;
        }

        return '';
    }

    /**
     * @return string
     */
    private function get_phpDoc () : string {

        if( empty($this->phpDoc) ) {
            return '';
        }

        return $this->phpDoc . PHP_EOL;
    }

    private function get_use () : string {

        if( empty($this->use) ) {
            return '';
        }
        $use = '';
        sort( $this->use );
        foreach ( $this->use as $use ) {
            $use .= "use {$use};" . PHP_EOL;
        }
        $use .= PHP_EOL;

        return $use;
    }


}
