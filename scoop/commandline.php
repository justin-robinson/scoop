<?php

namespace Scoop;

/**
 * CommandLine class
 * Command Line Interface (CLI) utility class.
 * @author              Patrick Fisher <patrick@pwfisher.com>
 * @since               August 21, 2009
 * @see                 https://github.com/pwfisher/CommandLine.php
 */
class CommandLine {

    public static $args;

    public static $numArgs;

    /**
     * @param array|null $argv
     *
     * @return array
     */
    public static function parse_args ( array $argv = null ) : array {

        $argv = $argv ? $argv : $_SERVER['argv'];

        // first element is the script name
        array_shift ( $argv );

        // how many args we have
        self::$numArgs = count ( $argv );

        // storage for our parsed args
        self::$args = [];

        // parse each arg
        for ( $argvIndex = 0; $argvIndex < self::$numArgs; $argvIndex++) {

            // get the arg
            $arg = $argv[$argvIndex];

            // --foo --bar=baz
            if ( substr ( $arg, 0, 2 ) === '--' ) {
                self::parse_long_opt($argv, $argvIndex);
            }
            // -k -k=value
            else if ( substr ( $arg, 0, 1 ) === '-' ) {
                self::parse_short_opt( $argv, $argvIndex );
            }
            // plain arg
            else {
                self::parse_opt($argv, $argvIndex);
            }
        }

        return self::$args;
    }

    /**
     * @param      $key
     * @param bool $default
     *
     * @return bool|mixed|string
     */
    public static function get_boolean ( $key, $default = false ) {

        if ( !isset( self::$args[$key] ) ) {
            return $default;
        }
        $value = self::$args[$key];

        if ( is_bool ( $value ) ) {
            return $value;
        }

        if ( is_int ( $value ) ) {
            return (bool) $value;
        }

        if ( is_string ( $value ) ) {
            $value = strtolower ( $value );
            $map = [
                'y'     => true,
                'n'     => false,
                'yes'   => true,
                'no'    => false,
                'true'  => true,
                'false' => false,
                '1'     => true,
                '0'     => false,
                'on'    => true,
                'off'   => false,
            ];
            if ( isset( $map[$value] ) ) {
                return $map[$value];
            }
        }

        return $default;
    }

    /**
     * @param $argv
     * @param $argvIndex
     */
    private static function parse_long_opt ( &$argv, &$argvIndex ) {

        $arg = $argv[$argvIndex];
        $nextIndex = $argvIndex + 1;

        // where the equal sign is
        $eqPos = strpos ( $arg, '=' );

        // --foo
        if ( $eqPos === false ) {
            $key = substr ( $arg, 2 );

            // --foo value
            if ( $nextIndex < self::$numArgs && $argv[$nextIndex][0] !== '-' ) {
                $value = $argv[$nextIndex];
                ++$argvIndex;
            } else {
                $value = isset( self::$args[$key] ) ? self::$args[$key] : true;
            }
        }
        // --bar=baz
        else {
            $key = substr ( $arg, 2, $eqPos - 2 );
            $value = substr ( $arg, $eqPos + 1 );
        }

        self::$args[$key] = $value;
    }

    /**
     * @param $argv
     * @param $argvIndex
     */
    private static function parse_short_opt ( &$argv, &$argvIndex ) {

        $arg = $argv[$argvIndex];
        $nextIndex = $argvIndex+1;

        // -k=value
        if ( substr ( $arg, 2, 1 ) === '=' ) {
            $key = substr ( $arg, 1, 1 );
            $value = substr ( $arg, 3 );
        }
        // -abc
        else {
            $chars = str_split ( substr ( $arg, 1 ) );
            foreach ( $chars as $char ) {
                $key = $char;
                $value = isset( $out[$key] ) ? $out[$key] : true;
            }
            // -a value1 -abc value2
            if ( $nextIndex < self::$numArgs && $argv[$nextIndex][0] !== '-' ) {
                $value = $argv[$nextIndex];
                ++$argvIndex;
            }
        }

        self::$args[$key] = $value;
    }

    /**
     * @param $argv
     * @param $argvIndex
     */
    private static function parse_opt ( &$argv, &$argvIndex ) {

        self::$args[] = $argv[$argvIndex];
    }
}
