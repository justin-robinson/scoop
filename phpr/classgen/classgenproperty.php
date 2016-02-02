<?php

namespace phpr\ClassGen;

/**
 * Class ClassGenProperty
 * @package phpr\ClassGen
 */
class ClassGenProperty extends ClassGenAbstract {

    /**
     * @var string
     */
    public $name;

    /**
     * @var null
     */
    public $value;

    /**
     * @var bool
     */
    public $isStatic;

    /**
     * ClassGenProperty constructor.
     * @param $name
     * @param null $value
     * @param bool $isStatic
     * @param int $visibility
     */
    public function __construct ( $name, $value = null, $isStatic = false, $visibility = self::VISIBILITY_PUBLIC ) {

        $this->name = $name;
        $this->value = $value;
        $this->isStatic = $isStatic;
        $this->visibility = $visibility;

    }

    /**
     * @return string
     */
    public function getHeader () : string {

        $propertyValue = var_export ( $this->value, true );

        if ( is_array ( $this->value ) ) {
            $propertyValue = PHP_EOL . preg_replace ( '/^/m', ClassGenGenerator::$indentation . ClassGenGenerator::$indentation, $propertyValue );
        }

        $line = ClassGenGenerator::$indentation;

        if ( $this->is_const () ) {
            $line .= 'const ';

            // convert camel and snake case to underscores
            $nameParts = preg_split ( '/([A-Z]+[^A-Z]+)/', $this->name, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
            $this->name = strtoupper ( implode ( '_', $nameParts ) );

        } else {
            $line .= $this->get_visibility ();
            $line .= ( $this->isStatic ) ? ' static ' : ' ';
        }

        $line .= $this->is_const () ? '' : '$';
        $line .= $this->name . ' = ' . $propertyValue;
        $line .= ';' . PHP_EOL;

        return $line;
    }

    /**
     * @return string
     */
    public function getFooter () : string {

        return '';

    }


}