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

    /**
     * @param array|null $argv
     *
     * @return array
     */
    public static function parseArgs ( array $argv = null ) : array {

        $argv = $argv ? $argv : $_SERVER['argv'];

        // first element is the script name
        array_shift ( $argv );

        $numArgs = count ( $argv );

        $out = [ ];

        for ( $i = 0, $j = $numArgs; $i < $j; $i++ ) {
            $arg = $argv[$i];

            // --foo --bar=baz
            if ( substr ( $arg, 0, 2 ) === '--' ) {
                $eqPos = strpos ( $arg, '=' );

                // --foo
                if ( $eqPos === false ) {
                    $key = substr ( $arg, 2 );

                    // --foo value
                    if ( $i + 1 < $j && $argv[$i + 1][0] !== '-' ) {
                        $value = $argv[$i + 1];
                        $i++;
                    } else {
                        $value = isset( $out[$key] ) ? $out[$key] : true;
                    }
                    $out[$key] = $value;
                } // --bar=baz
                else {
                    $key = substr ( $arg, 2, $eqPos - 2 );
                    $value = substr ( $arg, $eqPos + 1 );
                    $out[$key] = $value;
                }
            } // -k=value -abc
            else if ( substr ( $arg, 0, 1 ) === '-' ) {
                // -k=value
                if ( substr ( $arg, 2, 1 ) === '=' ) {
                    $key = substr ( $arg, 1, 1 );
                    $value = substr ( $arg, 3 );
                    $out[$key] = $value;
                } // -abc
                else {
                    $chars = str_split ( substr ( $arg, 1 ) );
                    foreach ( $chars as $char ) {
                        $key = $char;
                        $value = isset( $out[$key] ) ? $out[$key] : true;
                        $out[$key] = $value;
                    }
                    // -a value1 -abc value2
                    if ( $i + 1 < $j && $argv[$i + 1][0] !== '-' ) {
                        $out[$key] = $argv[$i + 1];
                        $i++;
                    }
                }
            } // plain-arg
            else {
                $value = $arg;
                $out[] = $value;
            }
        }

        self::$args = $out;

        return $out;
    }

    /**
     * GET BOOLEAN
     */
    public static function getBoolean ( $key, $default = false ) {

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
}
