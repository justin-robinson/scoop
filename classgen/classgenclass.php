<?

namespace ClassGen;

class ClassGenClass extends \ClassGen\ClassGenAbstract {

    public $name;
    public $extends;
    public $implements;
    public $phpDoc = '';
    public $namespace;

    public function __construct ( $name, $extends = null, $namespace = '', $implements = [] ) {
        $this->name = $name;
        $this->extends = $extends;
        $this->namespace = $namespace;
        $this->implements = $implements;
    }

    public function set_name ( $name ) {
        $this->name = $name;
    }
    public function set_extends ( $extends ) {
        $this->extends = $extends;
    }
    public function set_implements ( array $implements ) {
        $this->implements = $implements;
    }
    public function set_namespace ( $namespace ) {
        $this->namespace = $namespace;
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
namespace {$this->namespace};
" . $classModifier . "class {$this->name} ";

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