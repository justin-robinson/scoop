#!/usr/bin/env php
<?php

/**
 * Creates necessary folders and files needed for a site to function
 * args
 *  --site=example.com
 *      create example.com files and folders
 */

try {
    $args = require_once dirname ( __FILE__ ) . '/_script_core.php';
} catch ( mysqli_sql_exception $e ) {
}

if ( isset( $_SERVER['SCOOP_SITE_NAME'] ) ) {

    // get the classpath for this site
    $siteClassPath = \Scoop\Config::get_site_class_path ();
    $sitePath = pathinfo ( $siteClassPath, PATHINFO_DIRNAME );

    // ensure path exists
    @mkdir ( $siteClassPath, 0777, true );
    if ( !file_exists ( $siteClassPath ) ) {
        die( 'error creating class path folder. check permissions' );
    }

    // get config path for this site
    $siteConfigPath = $sitePath . '/' . \Scoop\Config::get_configpath_folder_name ();
    @mkdir ( $siteConfigPath );

    if ( !file_exists ( $siteConfigPath ) ) {
        die( 'error creating class path folder. check permissions' );
    }

    // copy default db config if one is not present
    $siteDbConfig = $siteConfigPath . '/db.php';
    if ( file_exists ( $siteDbConfig ) ) {
        echo 'db config already exists for `' . $_SERVER['SCOOP_SITE_NAME'] . '` at :' . realpath ( $siteDbConfig );
    } else {
        copy ( \Scoop\Config::get_Scoop_class_path () . '/configs/db.php', $siteDbConfig );
    }

} else {
    die( 'run with --site=example.com' );
}
