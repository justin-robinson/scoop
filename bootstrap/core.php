<?php

// where Scoop is installed
$installDirectory = realpath ( pathinfo ( __FILE__, PATHINFO_DIRNAME ) . '/..' ) . '/';

// get the config class
require_once $installDirectory . '/scoop/config.php';

// set the install location
\Scoop\Config::set_option ( 'install_dir', $installDirectory );
\Scoop\Config::set_option ( 'config_dir', $installDirectory . '/configs/' );
\Scoop\Config::set_option ( 'bootstrap_dir', $installDirectory . '/bootstrap' );

if ( !empty( $_SERVER['DOCUMENT_ROOT'] ) ) {
    \Scoop\Config::set_option ( 'server_document_root', $_SERVER['DOCUMENT_ROOT'] );
}

// search for the site name
if ( !empty( $_SERVER['SITE_NAME'] ) ) {
    $siteName = $_SERVER['SITE_NAME'];
} else if ( !empty( $_SERVER['SERVER_NAME'] ) ) {
    $siteName = $_SERVER['SERVER_NAME'];
}
if ( isset ( $siteName ) ) {
    \Scoop\Config::set_option ( 'site_name', $siteName );
}

// load config
$frameworkConfig = include \Scoop\Config::get_option ( 'config_dir' ) . '/framework.php';

// load user config file if one exists
foreach ([__DIR__ . '/../../scoop/custom.php',
          __DIR__ . '/../../../../scoop/custom.php'] as $customConfigFilePath) {
    if (file_exists($customConfigFilePath)) {
        $customConfig = include_once $customConfigFilePath;

        if ( is_array($customConfig) ) {
            $frameworkConfig = array_replace_recursive ( $frameworkConfig, $customConfig );
        }
    }
}

// set main options
\Scoop\Config::set_options ( $frameworkConfig );

// set the timezone if one was provided
if ( \Scoop\Config::option_exists ( 'timezone' ) ) {
    date_default_timezone_set ( \Scoop\Config::get_option ( 'timezone' ) );
}

// the autoloader
require_once ( \Scoop\Config::get_option ( 'bootstrap_dir' ) . '/autoloader.php' );

// show errors for internal ips
$clientIP = array_key_exists ( 'REMOTE_ADDR', $_SERVER ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
$serverIP = array_key_exists ( 'SERVER_ADDR', $_SERVER ) ? $_SERVER['SERVER_ADDR'] : '0.0.0.0';
if ( \Scoop\Environment::is_internal_ip ($clientIP, $serverIP) ) {
    ini_set ( 'display_errors', 'On' );
    ini_set ( 'display_startup_errors', 'On' );
} else {
    ini_set ( 'display_errors', 'Off' );
    ini_set ( 'display_startup_errors', 'Off' );
}

// connect to mysql server
Scoop\Database\Connection::connect ();
