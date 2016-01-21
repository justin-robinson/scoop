<?php

namespace phpr\ClassGen;

class ClassGenClass extends ClassGenAbstract {

    public $name;
    public $extends;
    public $implements;
    public $phpDoc = '';
    public $namespace;
    public $use = [];

    public function __construct ( $name, $extends = null, $namespace = '', $implements = [] ) {
        $this->name = $name;
        $this->extends = $extends;
        $this->namespace = $namespace;
        $this->implements = $implements;

        return $this;
    }

    public function set_name ( $name ) {
        $this->name = $name;
        return $this;
    }
    public function set_extends ( $extends ) {
        $this->extends = $extends;
        return $this;
    }
    public function set_implements ( array $implements ) {
        $this->implements = $implements;
        return $this;
    }
    public function set_namespace ( $namespace ) {
        $this->namespace = $namespace;
        return $this;
    }
    public function set_use ( array $use ) {
        $this->use = $use;
        return $this;
    }

    public function append_use( $use ) {
        $this->use[] = $use;
        return $this;
    }


    public function getHeader() : string {


        if ( $this->is_final() && $this->is_abstract() ) {
            throw new Error('Class can\'t be final and abstract');
        }

        $classModifierArray = [];

        if ( $this->is_final() ) {
            $classModifierArray[] = $this->modifierStrings[self::MODIFIER_FINAL];
        }

        if ( $this->is_abstract() ) {
            $classModifierArray[] = $this->modifierStrings[self::MODIFIER_ABSTRACT];
        }

        $classModifier = implode(' ', $classModifierArray);
        if ( !empty($classModifier) ) {
            $classModifier .= ' ';
        }


        // class name and phpdoc
        $header =
"<?
{$this->phpDoc}
";
        if ( !empty($this->namespace) ) {
            $header .= "namespace {$this->namespace};

";
        }

        if ( !empty($this->use) ) {
            foreach ( $this->use as $use ) {
                $header .= "use {$use};

";
            }
        }
        $header .= $classModifier . "class {$this->name} ";

        // class extends
        $header .= $this->get_extends_code();

        // class implements
        $header .= $this->get_implements_code();

        $header .= " {

";

        return $header;
    }

    public function getFooter() : string {
        $footer = "
}

?>";

        return $footer;
    }

    private function get_extends_code () {

        if ( empty($this->extends) ) {
            $code = '';
        } else {
            $code = 'extends ' . $this->extends;
        }

        return $code;
    }

    private function get_implements_code () {

        if ( empty($this->implements) ) {
            $code = '';
        } else {
            $code = 'implements ' . implode(',', $this->implements);
        }

        return $code;
    }



}