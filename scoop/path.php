<?php

namespace Scoop;

/**
 * Class Path
 * @package Scoop
 */
class Path {

    /**
     * @param $path
     *
     * @return string
     */
    public static function make_absolute ( $path ) {

        if ( !Path::is_absolute ( $path ) ) {
            $path = Config::get_option ( 'install_dir' ) . $path;
        }

        return $path;

    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function is_absolute ( $path ) {

        return strpos ( $path, '/' ) === 0;
    }

}
