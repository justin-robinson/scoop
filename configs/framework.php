<?php

$phprConfig = [
    'R_VERSION_MAJOR' => 0,
    'R_VERSION_MINOR' => 2,
    'R_VERSION_PATCH' => 0,

    /*
     * Path to global classes shared by all sites
     * Can be relative to phpr install directory
     * ($_SERVER['R_DOCUMENT_ROOT']) or an absolute path
     */
    'R_SHARED_CLASSPATH_PARENT_DIRECTORY' => '../',
    'R_CLASSPATH_FOLDER_NAME' => 'phpr-classes',
    'R_SITES_FOLDER' => '../'
];

$phrConfig['R_VERSION'] = 
    $phprConfig['R_VERSION_MAJOR'] . '.' .
    $phprConfig['R_VERSION_MINOR'] . '.' .
    $phprConfig['R_VERSION_PATCH'];

return $phprConfig;

