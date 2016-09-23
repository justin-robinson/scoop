<?php

namespace Scoop\ClassGen;

/**
 * Class ClassGenFunction
 * @package Scoop\ClassGen
 */
class ClassGenFunction extends ClassGenAbstract {

    public $name;

    public $args;

    public $body;

    public $isStatic;

    /**
     * ClassGenFunction constructor.
     * @param $name
     * @param string $args
     * @param null $body
     */
    public function __construct($name, $args = '', $body = null) {

        $this->name = $name;
        $this->args = $args;
        $this->body = $body === null
            ? "// TODO: Implement {$name}() function."
            : $body;
        $this->set_public();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function get_header() {

        // abstract function have a few rules
        if ($this->is_abstract()) {
            if ($this->is_final()) {
                throw new \Exception("Function can't be final and abstract");
            }

            if ($this->get_visibility() === self::VISIBILITY_PRIVATE) {
                throw new \Exception("Function can't be private and abstract");
            }

            if (!empty($this->body)) {
                throw new \Exception("Function can't be abstract and have a body");
            }
        }

        // the function name and modifiers
        $header = $this->is_final() ? 'final ' : '';
        $header .= $this->is_abstract() ? 'abstract ' : '';

        $visibility = $this->get_visibility();
        $header.= empty($visibility) ? '' : $visibility . ' ';
        $header .= 'function ' . $this->name . " ({$this->args})";

        $header .= $this->is_abstract()
            ? ';'
            : (' {' . PHP_EOL . ClassGenGenerator::$indentation . $this->body . PHP_EOL . '}');

        return $header;
    }

    /**
     * @return string
     */
    public function get_footer() {
        return '';
    }
}
