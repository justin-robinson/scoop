<?php

namespace phpr;

class Config {

    /**
     * @var
     * cache for get_class_paths()
     */
    private static $classPaths;

    /**
     * @var
     * cache for get_shared_class_path()
     */
    private static $sharedClassPath;

    /**
     * @var
     * cache for get_phpr_class_path()
     */
    private static $phprClassPath;

    /**
     * @var
     * cache for get_site_class_path()
     */
    private static $siteClassPath;

    /**
     * @return array
     * Gets all possible class paths
     */
    public static function get_class_paths() {

        if ( is_null(self::$classPaths) ) {

            $classPaths = [];

            $classPaths[] = self::get_site_class_path();
            $classPaths[] = self::get_phpr_class_path();
            $classPaths[] = self::get_shared_class_path();

            self::$classPaths = $classPaths;
        }

        return self::$classPaths;
    }

    /**
     * @return string
     * Gets classpath shared by all sites
     */
    public static function get_shared_class_path () {

        if ( is_null(self::$sharedClassPath) ) {

            $sharedClassPath = Path::make_absolute(
                $_SERVER['R_SHARED_CLASSPATH_PARENT_DIRECTORY'] . $_SERVER['R_CLASSPATH_FOLDER_NAME']);

            self::$sharedClassPath = $sharedClassPath;
        }

        return self::$sharedClassPath;

    }

    /**
     * @return mixed
     * Gets classpath for native phpr classes
     */
    public static function get_phpr_class_path() {

        if ( is_null(self::$phprClassPath) ) {
            self::$phprClassPath = $_SERVER['R_DOCUMENT_ROOT'];
        }
        return self::$phprClassPath;
    }

    /**
     * @return string
     * Gets classpath for the current site
     */
    public static function get_site_class_path() {

        if ( is_null(self::$siteClassPath) ) {

            if ( !empty($_SERVER['DOCUMENT_ROOT']) ) {
                self::$siteClassPath = $_SERVER['DOCUMENT_ROOT'];
            } else if ( array_key_exists('R_SITE_NAME', $_SERVER) ) {
                self::$siteClassPath = self::get_sites_folder() . '/' . $_SERVER['R_SITE_NAME'];
            }

            self::$siteClassPath .= '/'. $_SERVER['R_CLASSPATH_FOLDER_NAME'];
        }
        return self::$siteClassPath;
    }

    public static function get_site_class_path_by_name ($siteName ) {

        self::$siteClassPath =  self::get_sites_folder() . '/'.  $siteName . '/' . $_SERVER['R_CLASSPATH_FOLDER_NAME'];

        return self::$siteClassPath;
    }

    public static function get_sites_folder () {

        return Path::make_absolute($_SERVER['R_SITES_FOLDER']);

    }

    public static function get_db_config () {

        require_once self::get_phpr_class_path() . '/configs/db.php';

        include_once self::get_site_class_path() . '/../phpr-configs/db.php';

        return $phprDB;
    }
}