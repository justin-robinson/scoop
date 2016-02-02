<?php

namespace phpr\ClassGen;

/**
 * Class ClassGenAbstract
 * @package phpr\ClassGen
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
        self::VISIBILITY_PROTECTED => 'protected'
    ];

    /**
     * @var array
     */
    public $modifierStrings = [
        self::MODIFIER_FINAL    => 'final',
        self::MODIFIER_ABSTRACT => 'abstract',
        self::MODIFIER_CONST    => 'const'
    ];

    /**
     * @var string
     */
    public $visibility;

    public $modifiers = [
        self::MODIFIER_FINAL    => false,
        self::MODIFIER_ABSTRACT => false,
        self::MODIFIER_CONST    => false
    ];

    /**
     * @return string
     */
    public function get () : string {

        return $this->getHeader () . $this->getFooter ();
    }

    /**
     * @return string
     */
    public function get_visibility () : string {

        return $this->visibilityStrings[$this->visibility];
    }

    /**
     * @return bool
     */
    public function is_abstract () : bool {

        return $this->modifiers[self::MODIFIER_ABSTRACT];
    }

    /**
     * @return bool
     */
    public function is_const () : bool {

        return $this->modifiers[self::MODIFIER_CONST];
    }

    /**
     * @return bool
     */
    public function is_final () : bool {

        return $this->modifiers[self::MODIFIER_FINAL];
    }

    /**
     * @return string
     */
    abstract function getHeader () : string;

    /**
     * @return string
     */
    abstract function getFooter () : string;

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
    public function set_final ( bool $isFinal = true ) {

        $this->modifiers[self::MODIFIER_FINAL] = $isFinal;
    }

    /**
     * @param bool $isAbstract
     */
    public function set_abstract ( bool $isAbstract = true ) {

        $this->modifiers[self::MODIFIER_ABSTRACT] = $isAbstract;
    }

    /**
     * @param bool $isAbstract
     */
    public function set_const ( bool $isAbstract = true ) {

        $this->modifiers[self::MODIFIER_CONST] = $isAbstract;
    }

}
