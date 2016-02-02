<?php

/**************
 * composer
 * ************/
$composerAutoloaderPath = $_SERVER['R_DOCUMENT_ROOT'] . '/vendor/autoload.php';
if ( file_exists ( $composerAutoloaderPath ) ) {
    require_once $composerAutoloaderPath;
}

/**************
 * phpr
 **************/
require_once $_SERVER['R_DOCUMENT_ROOT'] . '/phpr/config.php';
require_once $_SERVER['R_DOCUMENT_ROOT'] . '/phpr/path.php';

// load all classpaths that exist
foreach ( \phpr\Config::get_class_paths () as $classPath ) {
    if ( $classPath = realpath ( $classPath ) ) {
        set_include_path ( get_include_path () . PATH_SEPARATOR . $classPath . '/' );
    }
}

spl_autoload_register (
    function ( $className ) {

        spl_autoload ( $className, '.php' );
    }, true, true );

