<?php

namespace phpr;

/**
 * Class Path
 * @package phpr
 */
class Path {

    /**
     * @param $path
     * @return bool
     */
    public static function is_absolute ( $path ) {

        return strpos ( $path, '/' ) === 0;
    }

    /**
     * @param $path
     * @return string
     */
    public static function make_absolute ( $path ) {

        if ( !Path::is_absolute ( $path ) ) {
            $path = $_SERVER['R_DOCUMENT_ROOT'] . $path;
        }

        return $path;

    }

}