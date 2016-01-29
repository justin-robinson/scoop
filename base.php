<?php

// allow <? as well as <?php for opening php tag
ini_set('short_open_tag', 'On');

// where phpr is installed
$installDirectory = pathinfo(__FILE__, PATHINFO_DIRNAME) . '/';

// set the R variable
$_SERVER['R_DOCUMENT_ROOT'] = $installDirectory;

// load config into server variable
$phprConfig = require_once $_SERVER['R_DOCUMENT_ROOT'] . '/configs/framework.php';
$_SERVER = array_merge($_SERVER, $phprConfig);

// set the timezone if one was provided
if ( isset( $_SERVER['R_TIMEZONE'] ) ) {
    date_default_timezone_set($_SERVER['R_TIMEZONE']);
}

// the autoloader
require_once($_SERVER['R_DOCUMENT_ROOT'] . '/autoloader.php');

// show errors for internal ips
if ( \phpr\Environment::is_internal_ip() ) {
    ini_set('display_errors', 'On');
    ini_set('display_startup_errors', 'On');
} else {
    ini_set('display_errors', 'Off');
    ini_set('display_startup_errors', 'Off');
}


// connect to mysql server
phpr\Database\Connection::connect();

// setup mysqli statement cache
phpr\Database\Model\Generic::$statementCache = new phpr\Database\Cache\Statement();

date_default_timezone_set($phprConfig['R_TIMEZONE']);

function serverError($message) {
    header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500);
    echo $message;
    die;
}

// makes sql safe
function r3a($sqlString, $quoteChar = "'") {
    $sqlString = phpr\Database\Connection::real_escape_string($sqlString);
    return $quoteChar . addslashes($sqlString) . $quoteChar;
}

// handles values to sql strings
function print_sql($value, $quoteChar = "'"){
    if ( is_null($value) ) {
        $sqlValue = 'NULL';
    } else if ( is_object($value) && is_a($value, 'phpr\Database\Literal') ) {
        $sqlValue = (string)$value;
    } else {
        $sqlValue = r3a($value, $quoteChar);
    }

    return $sqlValue;
}

function r3a_array( &$array, $quoteChar ) {

    foreach ( $array as $index => &$value ) {

        $value = print_sql($value, $quoteChar);

    }

}
