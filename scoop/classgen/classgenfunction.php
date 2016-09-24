<?php

namespace Scoop\ClassGen;

/**
 * Class ClassGenFunction
 * @package Scoop\ClassGen
 */
class ClassGenFunction extends ClassGenAbstract {

    /**
     * @var
     */
    public $name;

    /**
     * @var string
     */
    public $args;

    /**
     * @var null|string
     */
    public $body;

    /**
     * @var bool
     */
    private $isStatic;

    /**
     * @var string
     */
    private $phpdoc;

    /**
     * ClassGenFunction constructor.
     * @param $name
     * @param string $args
     * @param null $body
     * @param string $phpdoc
     */
    public function __construct($name, $args = '', $body = null, $phpdoc = '') {

        $this->name = $name;
        $this->args = $args;
        $this->body = $body === null
            ? "// TODO: Implement {$name}() function."
            : $body;
        $this->phpdoc = $phpdoc;
        $this->isStatic = false;
        $this->set_public();
    }

    /**
     * @param string $phpdoc
     */
    public function set_phpdoc($phpdoc) {
        $this->phpdoc = $phpdoc;
    }

    /**
     * @param $isStatic bool
     */
    public function set_static ( $isStatic = true ) {

        $this->isStatic = (bool)$isStatic;
    }

    /**
     * @return string
     */
    public function get_phpdoc() {
        return $this->phpdoc;
    }

    /**
     * @return bool
     */
    public function is_static() {
        return $this->isStatic;
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

            if ($this->get_visibility() === 'private') {
                throw new \Exception("Function can't be private and abstract");
            }

            if (!empty($this->body)) {
                throw new \Exception("Function can't be abstract and have a body");
            }
        }

        // the function name and modifiers
        $header = empty($this->get_phpdoc()) ? '' : $this->get_phpdoc() . PHP_EOL;
        $header .= $this->is_final() ? 'final ' : '';
        $header .= $this->is_abstract() ? 'abstract ' : '';
        $header .= $this->is_static() ? 'static ' : '';

        $visibility = $this->get_visibility();
        $header.= empty($visibility) ? '' : $visibility . ' ';
        $header .= 'function ' . $this->name . " ({$this->args})";

        $body = empty($this->body) ? '' : ClassGenGenerator::$indentation . $this->body . PHP_EOL;

        $header .= $this->is_abstract()
            ? ';'
            : (' {' . PHP_EOL . $body . '}');

        return $header;
    }

    /**
     * @return string
     */
    public function get_footer() {
        return '';
    }
}
