<?

abstract class Class_Abstract {

    const VISIBILITY_PUBLIC = 0;
    const VISIBILITY_PRIVATE = 1;
    const VISIBILITY_PROTECTED = 2;

    const MODIFIER_FINAL = 0;
    const MODIFIER_ABSTRACT = 1;
    const MODIFIER_CONST = 2;

    public $visibilityStrings = [
        self::VISIBILITY_PUBLIC => 'public',
        self::VISIBILITY_PRIVATE => 'private',
        self::VISIBILITY_PROTECTED => 'protected'
    ];

    public $modifierStrings = [
        self::MODIFIER_FINAL => 'final',
        self::MODIFIER_ABSTRACT => 'abstract',
        self::MODIFIER_CONST => 'const'
    ];

    public $visibility;

    public $modifiers = [
        self::MODIFIER_FINAL => false,
        self::MODIFIER_ABSTRACT => false,
        self::MODIFIER_CONST => false
    ];

    /*
     * Visibility setters
     */
    public function set_public () {
        $this->visibility = self::VISIBILITY_PUBLIC;
    }
    public function set_private () {
        $this->visibility = self::VISIBILITY_PRIVATE;
    }
    public function set_protected () {
        $this->visibility = self::VISIBILITY_PROTECTED;
    }

    /*
     * Modifier setters
     */
    public function set_final ( bool $isFinal = true ) {
        $this->modifiers[self::MODIFIER_FINAL] = $isFinal;
    }
    public function set_abstract ( bool $isAbstract = true ) {
        $this->modifiers[self::MODIFIER_ABSTRACT] = $isAbstract;
    }
    public function set_const ( bool $isAbstract = true ) {
        $this->modifiers[self::MODIFIER_CONST] = $isAbstract;
    }

    /*
     * getters
     */
    public function get_visibility () : string {
        return $this->visibilityStrings[$this->visibility];
    }
    public function is_final () : bool {
        return $this->modifiers[self::MODIFIER_FINAL];
    }
    public function is_abstract () : bool {
        return $this->modifiers[self::MODIFIER_ABSTRACT];
    }
    public function is_const () : bool {
        return $this->modifiers[self::MODIFIER_CONST];
    }

    public function get() : string {
        return $this->getHeader() . $this->getFooter();
    }

    abstract function getHeader() : string;
    abstract function getFooter() : string;

}