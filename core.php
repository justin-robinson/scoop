<?php

// where Scoop is installed
$installDirectory = pathinfo ( __FILE__, PATHINFO_DIRNAME ) . '/';

// set the R variable
$_SERVER['SCOOP_DOCUMENT_ROOT'] = $installDirectory;

// load config into server variable
$ScoopConfig = require_once $_SERVER['SCOOP_DOCUMENT_ROOT'] . '/configs/framework.php';
$_SERVER = array_merge ( $_SERVER, $ScoopConfig );

// load user config file if one exists
$userConfigFilePath = $_SERVER['SCOOP_DOCUMENT_ROOT'] . '/configs/custom.php';
if ( file_exists ( $userConfigFilePath ) ) {
    $userConfig = require_once $userConfigFilePath;
    $_SERVER = array_replace_recursive ( $_SERVER, $userConfig );
}

// set the timezone if one was provided
if ( isset( $_SERVER['SCOOP_TIMEZONE'] ) ) {
    date_default_timezone_set ( $_SERVER['SCOOP_TIMEZONE'] );
}

// the autoloader
require_once ( $_SERVER['SCOOP_DOCUMENT_ROOT'] . '/autoloader.php' );

// show errors for internal ips
if ( \Scoop\Environment::is_internal_ip () ) {
    ini_set ( 'display_errors', 'On' );
    ini_set ( 'display_startup_errors', 'On' );
} else {
    ini_set ( 'display_errors', 'Off' );
    ini_set ( 'display_startup_errors', 'Off' );
}

// connect to mysql server
Scoop\Database\Connection::connect ();
