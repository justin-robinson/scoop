<?php

namespace Scoop;

/**
 * Class Config
 * @package Scoop
 */
class Config {

    private static $options = [ ];

    /**
     * @return array
     * Gets all possible class paths
     */
    public static function get_class_paths () : array {

        return [
            self::get_site_class_path (),
            self::get_shared_class_path (),
            self::get_option ( 'install_dir' ),
        ];
    }

    /**
     * @return string
     * Gets classpath for the current site
     */
    public static function get_site_class_path () : string {

        if ( self::option_exists ( 'server_document_root' ) ) {
            $siteClassPath = self::get_option ( 'server_document_root' );
        } else if ( self::option_exists ( 'site_name' ) ) {
            $siteClassPath = self::get_sites_folder () . '/' . self::get_option ( 'site_name' );
        } else {
            $siteClassPath = '';
        }

        return $siteClassPath . '/' . self::get_option ( 'classpath_folder_name' );

    }

    public static function option_exists ( $name ) {

        return array_key_exists ( $name, self::$options );
    }

    public static function get_option ( $name ) {

        return array_key_exists ( $name, self::$options ) ? self::$options[$name] : null;
    }

    /**
     * @return string
     */
    public static function get_sites_folder () : string {

        return Path::make_absolute ( self::get_option ( 'sites_folder' ) );

    }

    /**
     * @return string
     * Gets classpath shared by all sites
     */
    public static function get_shared_class_path () : string {

        if ( self::option_exists ( 'shared_classpath_parent_directory' ) ) {

            return Path::make_absolute (
                self::get_option ( 'shared_classpath_parent_directory' ) . self::get_option ( 'classpath_folder_name' ) );
        }

        return null;

    }

    /**
     * @return array
     */
    public static function get_db_config () : array {

        $ScoopDB = require self::get_option ( 'install_dir' ) . '/configs/db.php';

        $siteDBConfigPath = self::get_site_class_path ()
            . '/../' . self::get_option ( 'configpath_folder_name' ) . '/db.php';

        if ( file_exists ( $siteDBConfigPath ) ) {
            $siteDB = require $siteDBConfigPath;
            $ScoopDB = array_replace_recursive ( $ScoopDB, $siteDB );
        }

        return $ScoopDB;
    }

    /**
     * @param $siteName
     *
     * @return string
     */
    public static function get_site_class_path_by_name ( $siteName ) {

        return self::get_sites_folder () . '/' . $siteName . '/' . self::get_option ( 'classpath_folder_name' );
    }

    public static function set_options ( array $options ) {

        self::$options = array_merge_recursive ( self::$options, $options );
    }

    public static function set_option ( $name, $option ) {

        self::$options[$name] = $option;
    }
}
