<?php

// where phpr is installed
$installDirectory = pathinfo ( __FILE__, PATHINFO_DIRNAME ) . '/';

// set the R variable
$_SERVER['R_DOCUMENT_ROOT'] = $installDirectory;

// load config into server variable
$phprConfig = require_once $_SERVER['R_DOCUMENT_ROOT'] . '/configs/framework.php';
$_SERVER = array_merge ( $_SERVER, $phprConfig );

// load user config file if one exists
$userConfigFilePath = $_SERVER['R_DOCUMENT_ROOT'] . '/configs/custom.php';
if ( file_exists ( $userConfigFilePath ) ) {
    $userConfig = require_once $userConfigFilePath;
    $_SERVER = array_replace_recursive ( $_SERVER, $userConfig );
}

// set the timezone if one was provided
if ( isset( $_SERVER['R_TIMEZONE'] ) ) {
    date_default_timezone_set ( $_SERVER['R_TIMEZONE'] );
}

// the autoloader
require_once ( $_SERVER['R_DOCUMENT_ROOT'] . '/autoloader.php' );

// show errors for internal ips
if ( \phpr\Environment::is_internal_ip () ) {
    ini_set ( 'display_errors', 'On' );
    ini_set ( 'display_startup_errors', 'On' );
} else {
    ini_set ( 'display_errors', 'Off' );
    ini_set ( 'display_startup_errors', 'Off' );
}

// connect to mysql server
phpr\Database\Connection::get_instance ();

// setup mysqli statement cache
phpr\Database\Model\Generic::$statementCache = new phpr\Database\Cache\Statement();

date_default_timezone_set ( $phprConfig['R_TIMEZONE'] );

/**
 * @param $message
 */
function serverError ( $message ) {

    header ( $_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500 );
    echo $message;
    die;
}
