<?php

$ScoopConfig = [
    'SCOOP_VERSION_MAJOR'                     => 0,
    'SCOOP_VERSION_MINOR'                     => 3,
    'SCOOP_VERSION_PATCH'                     => 1,

    /*
     * Path to global classes shared by all sites
     * Can be relative to Scoop install directory
     * ($_SERVER['SCOOP_DOCUMENT_ROOT']) or an absolute path
     */
    'SCOOP_SHARED_CLASSPATH_PARENT_DIRECTORY' => '../',
    'SCOOP_CLASSPATH_FOLDER_NAME'             => 'scoop-classes',
    'SCOOP_CONFIGPATH_FOLDER_NAME'            => 'scoop-configs',
    'SCOOP_SITES_FOLDER'                      => '../',
];

$ScoopConfig['SCOOP_VERSION'] =
    $ScoopConfig['SCOOP_VERSION_MAJOR'] . '.' .
    $ScoopConfig['SCOOP_VERSION_MINOR'] . '.' .
    $ScoopConfig['SCOOP_VERSION_PATCH'];

$ScoopConfig['SCOOP_TIMEZONE'] = 'America/New_York';

return $ScoopConfig;
