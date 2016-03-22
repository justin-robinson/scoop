<?php

namespace Scoop\ClassGen;

/**
 * Class ClassGenAbstract
 * @package Scoop\ClassGen
 */
abstract class ClassGenAbstract {

    const VISIBILITY_PUBLIC = 0;

    const VISIBILITY_PRIVATE = 1;

    const VISIBILITY_PROTECTED = 2;

    const MODIFIER_FINAL = 0;

    const MODIFIER_ABSTRACT = 1;

    const MODIFIER_CONST = 2;

    /**
     * @var array
     */
    public $visibilityStrings = [
        self::VISIBILITY_PUBLIC    => 'public',
        self::VISIBILITY_PRIVATE   => 'private',
        self::VISIBILITY_PROTECTED => 'protected',
    ];

    /**
     * @var array
     */
    public $modifierStrings = [
        self::MODIFIER_FINAL    => 'final',
        self::MODIFIER_ABSTRACT => 'abstract',
        self::MODIFIER_CONST    => 'const',
    ];

    /**
     * @var string
     */
    public $visibility;

    public $modifiers = [
        self::MODIFIER_FINAL    => false,
        self::MODIFIER_ABSTRACT => false,
        self::MODIFIER_CONST    => false,
    ];

    /**
     * @return string
     */
    public function get () {

        return $this->getHeader () . $this->getFooter ();
    }

    /**
     * @return string
     */
    abstract function getHeader ();

    /**
     * @return string
     */
    abstract function getFooter ();

    /**
     * @return string
     */
    public function get_visibility () {

        return $this->visibilityStrings[$this->visibility];
    }

    /**
     * @return bool
     */
    public function is_abstract () {

        return $this->modifiers[self::MODIFIER_ABSTRACT];
    }

    /**
     * @return bool
     */
    public function is_const () {

        return $this->modifiers[self::MODIFIER_CONST];
    }

    /**
     * @return bool
     */
    public function is_final () {

        return $this->modifiers[self::MODIFIER_FINAL];
    }

    public function set_public () {

        $this->visibility = self::VISIBILITY_PUBLIC;
    }

    public function set_private () {

        $this->visibility = self::VISIBILITY_PRIVATE;
    }

    public function set_protected () {

        $this->visibility = self::VISIBILITY_PROTECTED;
    }

    /**
     * @param bool $isFinal
     */
    public function set_final ( $isFinal = true ) {

        $this->modifiers[self::MODIFIER_FINAL] = $isFinal;
    }

    /**
     * @param bool $isAbstract
     */
    public function set_abstract ( $isAbstract = true ) {

        $this->modifiers[self::MODIFIER_ABSTRACT] = $isAbstract;
    }

    /**
     * @param bool $isAbstract
     */
    public function set_const ( $isAbstract = true ) {

        $this->modifiers[self::MODIFIER_CONST] = $isAbstract;
    }

}
