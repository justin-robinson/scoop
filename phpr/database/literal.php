<?php

namespace phpr\Database;

/**
 * Class Literal
 * @package phpr\Database
 */
class Literal {

    /**
     * @var string
     */
    public $value;

    /**
     * Literal constructor.
     *
     * @param $value
     */
    public function __construct ( $value ) {

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString () : string {

        return $this->value;
    }
}
