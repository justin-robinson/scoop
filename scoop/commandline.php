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
    public static function parse_args ( array $argv = null ) {

        $argv = $argv ? $argv : $_SERVER['argv'];

        // first element is the script name
        array_shift ( $argv );

        self::$numArgs = count ( $argv );

        self::$args = [];

        foreach ( $argv as $argIndex => $arg ) {

            if ( !array_key_exists($argIndex, $argv) ) {
                continue;
            }

            // --foo --bar=baz
            if ( substr ( $arg, 0, 2 ) === '--' ) {
                self::parse_long_opt($argv, $argIndex);
            }
            else if ( substr ( $arg, 0, 1 ) === '-' ) {
                self::parse_short_opt( $argv, $argIndex );
            }
            else {
                self::parse_opt($argv, $argIndex);
            }
        }

        return self::$args;
    }

    /**
     * GET BOOLEAN
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
     * @param $index
     */
    private static function parse_long_opt ( &$argv, $index ) {

        $arg = $argv[$index];
        $nextIndex = $index + 1;

        $eqPos = strpos ( $arg, '=' );

        // --foo
        if ( $eqPos === false ) {
            $key = substr ( $arg, 2 );

            // --foo value
            if ( $nextIndex < self::$numArgs && $argv[$nextIndex][0] !== '-' ) {
                $value = $argv[$nextIndex];
                unset($argv[$nextIndex]);
            } else {
                $value = isset( self::$args[$key] ) ? self::$args[$key] : true;
            }
        } // --bar=baz
        else {
            $key = substr ( $arg, 2, $eqPos - 2 );
            $value = substr ( $arg, $eqPos + 1 );
        }

        self::$args[$key] = $value;
    }

    /**
     * @param $argv
     * @param $index
     */
    private static function parse_short_opt ( &$argv, $index ) {

        $arg = $argv[$index];
        $nextIndex = $index+1;

        // -k=value
        if ( substr ( $arg, 2, 1 ) === '=' ) {
            $key = substr ( $arg, 1, 1 );
            $value = substr ( $arg, 3 );
        } // -abc
        else {
            $chars = str_split ( substr ( $arg, 1 ) );
            foreach ( $chars as $char ) {
                $key = $char;
                $value = isset( $out[$key] ) ? $out[$key] : true;
            }
            // -a value1 -abc value2
            if ( $nextIndex < self::$numArgs && $argv[$nextIndex][0] !== '-' ) {
                $value = $argv[$nextIndex];
                unset($argv[$nextIndex]);
            }
        }

        self::$args[$key] = $value;
    }

    /**
     * @param $argv
     * @param $index
     */
    private static function parse_opt ( &$argv, $index ) {

        self::$args[] = $argv[$index];
    }
}
