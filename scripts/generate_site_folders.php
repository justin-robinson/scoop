<?php

$opts = require_once dirname ( __FILE__ ) . '/_script_header.php';

if ( isset( $_SERVER['R_SITE_NAME'] ) ) {

    // get the classpath for this site
    $siteClassPath = \phpr\Config::get_site_class_path();
    $sitePath = pathinfo($siteClassPath, PATHINFO_DIRNAME);

    // ensure path exists
    @mkdir($siteClassPath, 0777, true);
    if ( !file_exists($siteClassPath) ) {
        die('error creating class path folder. check permissions');
    }

    // get config path for this site
    $siteConfigPath = $sitePath . '/' . \phpr\Config::get_configpath_folder_name();
    @mkdir($siteConfigPath);

    if ( !file_exists($siteConfigPath) ) {
        die('error creating class path folder. check permissions');
    }

    // copy default db config
    copy(\phpr\Config::get_phpr_class_path() . '/configs/db.php', $siteConfigPath . '/db.php');

    $one = 1;

} else {
    die('run with --site=example.com');
}