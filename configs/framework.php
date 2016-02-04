<?php

$ScoopConfig = [
    'version_major'                     => 0,
    'version_minor'                     => 3,
    'version_patch'                     => 1,

    /*
     * Path to global classes shared by all sites
     * Can be relative to Scoop install directory
     * \Scoop\Config::get_option('install_dir') or an absolute path
     */
    'shared_classpath_parent_directory' => '../',
    'classpath_folder_name'             => 'scoop-classes',
    'configpath_folder_name'            => 'scoop-configs',
    'sites_folder'                      => '../',
];

$ScoopConfig['version'] =
    $ScoopConfig['version_major'] . '.' .
    $ScoopConfig['version_minor'] . '.' .
    $ScoopConfig['version_patch'];

$ScoopConfig['timezone'] = 'America/New_York';

return $ScoopConfig;
