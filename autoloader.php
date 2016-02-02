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

/**
 * 'spl_autoload' | use built-in psr-0 spl_autoload() 
 * true           | error thrown if 'spl_autoload' not found
 * true           | prepend this autoloader to the beginning of the autoload queue
 */
spl_autoload_register ('spl_autoload', true, true );

