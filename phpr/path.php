<?php

namespace phpr;

class Path {

    public static function is_absolute ( $path ) {
        return strpos($path, '/') === 0;
    }

    public static function make_absolute ($path ) {

        if ( !Path::is_absolute($path) ) {
            $path = $_SERVER['R_DOCUMENT_ROOT'] . $path;
        }

        return $path;

    }

}