<?

namespace ClassGen;

class ClassGenProperty extends \ClassGen\ClassGenAbstract {

    public $name;
    public $value;
    public $isStatic;

    public function __construct ($name, $value = null, $isStatic = false, $visibility = self::VISIBILITY_PUBLIC) {

        $this->name = $name;
        $this->value = $value;
        $this->isStatic = $isStatic;
        $this->visibility = $visibility;

    }

    public function getHeader () : string {

        $propertyValue = var_export($this->value, true);

        if ( is_array($this->value) ) {
            $propertyValue = PHP_EOL . preg_replace('/^/m', \ClassGen\ClassGenGenerator::$indentation . \ClassGen\ClassGenGenerator::$indentation, $propertyValue);
        }

        $line = \ClassGen\ClassGenGenerator::$indentation;

        if ( $this->is_const() ) {
            $line .= 'const ';

            // convert camel and snake case to underscores
            $nameParts = preg_split('/([A-Z]+[^A-Z]+)/', $this->name, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $this->name = strtoupper(implode('_', $nameParts));

        } else {
            $line .= $this->get_visibility();
            $line .= ($this->isStatic) ? ' static ' : ' ';
        }

        $line .= $this->is_const() ? '' : '$';
        $line .= $this->name . ' = ' . $propertyValue;
        $line .= ';' . PHP_EOL;

        return $line;
    }

    public function getFooter () : string {

        return '';

    }


}