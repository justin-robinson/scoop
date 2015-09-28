<?

class Database_Literal {

    public $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function __toString () {
        return $this->value;
    }
}