<?php

namespace Scoop\Exception\Database;

/**
 * Class Mysql
 * @package Scoop\Exception\Database
 */
class Mysql extends \Exception {

    /**
     * Mysql constructor.
     *
     * @param string          $message
     * @param string          $code
     * @param \Exception|null $previous
     * @param string          $sql
     */
    public function __construct($message = '', $code = '', \Exception $previous = null, $sql = '')
    {

        $message = "MySQL Error Number ( '$code' ) $message " . PHP_EOL . $sql . PHP_EOL;

        parent::__construct($message, $code, $previous);
    }

}
