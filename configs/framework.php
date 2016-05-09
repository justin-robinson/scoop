<?php

$ScoopConfig = [
    'version_major'                     => 0,
    'version_minor'                     => 1,
    'version_patch'                     => 0,

    /*
     * Path to global classes shared by all sites
     * Can be relative to Scoop install directory
     * \Scoop\Config::get_option('install_dir') or an absolute path
     */
    'shared_classpath_parent_directory' => '../../../scoop/',
    'classpath_folder_name'             => 'classes',
    'configpath_folder_name'            => 'configs',
    'sites_folder'                      => '../',
];

$ScoopConfig['version'] =
    $ScoopConfig['version_major'] . '.' .
    $ScoopConfig['version_minor'] . '.' .
    $ScoopConfig['version_patch'];

$ScoopConfig['timezone'] = 'America/New_York';

return $ScoopConfig;
