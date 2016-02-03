<?php

/**************
 * composer
 * ************/
$composerAutoloaderPath = $_SERVER['SCOOP_DOCUMENT_ROOT'] . '/vendor/autoload.php';
if ( file_exists ( $composerAutoloaderPath ) ) {
    require_once $composerAutoloaderPath;
}

/**************
 * Scoop
 **************/
require_once $_SERVER['SCOOP_DOCUMENT_ROOT'] . '/scoop/config.php';
require_once $_SERVER['SCOOP_DOCUMENT_ROOT'] . '/scoop/path.php';

// load all classpaths that exist
foreach ( \Scoop\Config::get_class_paths () as $classPath ) {
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
