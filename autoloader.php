<?php

require_once $_SERVER['R_DOCUMENT_ROOT'] . '/phpr/config.php';
require_once $_SERVER['R_DOCUMENT_ROOT'] . '/phpr/path.php';

function r_autoloader( $fullClassPath ) {

    // class names can be uppercase but files are lower case
    $fullClassPath = strtolower($fullClassPath);

    // an array containing each folder to get to the file
    $foldersArray = explode('\\', $fullClassPath);

    // last item is the file and class name
    $className = array_pop($foldersArray);

    // glue folders together
    $folders = implode('/', $foldersArray);

    // full path to the file
    $relativePath = '/' . $folders . '/' . $className . '.php';

    $fileExists = false;
    foreach ( \phpr\Config::get_class_paths() as $classPath ) {
        $filepath = $classPath . $relativePath;

        if ( $fileExists = file_exists($filepath) ) {
            include_once( $filepath );
            break;
        }
    }
}

spl_autoload_register('r_autoloader');
