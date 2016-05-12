<?php

namespace Scoop;

/**
 * Class Environment
 * @package Scoop
 */
class Environment {

    /**
     * Environment constants
     */
    const ENV_PROD = 1;

    const ENV_STAGING = 2;

    const ENV_TEST = 3;

    const ENV_LOCAL = 4;

    /**
     * gets integer representation of environment
     *
     * @return int
     */
    public static function get_environment () {

        $hostnameParts = explode ( '.', self::get_server_name () );

        switch ( $hostnameParts[0] ) {
            case 'localhost':
                return self::ENV_LOCAL;
            case 'test':
                return self::ENV_TEST;
            case 'staging':
                return self::ENV_STAGING;
            default:
                return self::ENV_PROD;
        }
    }

    /**
     * @return string
     * Searches known variables for a hostname
     */
    public static function get_server_name () {

        if ( array_key_exists ( 'SERVER_NAME', $_SERVER ) && !empty( $_SERVER['SERVER_NAME'] ) ) {
            $hostname = $_SERVER['SERVER_NAME'];
        } else if ( array_key_exists ( 'HOSTNAME', $_SERVER ) && !empty( $_SERVER['HOSTNAME'] ) ) {
            $hostname = $_SERVER['HOSTNAME'];
        } else {
            $hostname = gethostname ();
        }

        return $hostname;

    }

    /**
     * Determine if client is on an internal network
     *
     * @param string $clientIP
     * @param string $serverIP
     *
     * @return bool
     */
    public static function is_internal_ip ( $clientIP = '0.0.0.0', $serverIP = '0.0.0.0' ) {

        // does the client's ip match the server?
        if ( $clientIP === $serverIP ) {
            return true;
        }

        $octets = explode ( '.', $clientIP );
        if ( count($octets) !== 4 ) {
            return false;
        }
        /*
         * The Internet Assigned Numbers Authority (IANA) has reserved the following three blocks
         * of the IP address space for private internets:
         *
         * 10.0.0.0 - 10.255.255.255 (10/8 prefix)
         * 172.16.0.0 - 172.31.255.255 (172.16/12 prefix)
         * 192.168.0.0 - 192.168.255.255 (192.168/16 prefix)
         */
        return
            $octets[0] === '10'
            || $octets[0] === '172' && ( $octets[1] >= '16' && $octets[1] <= '31' )
            || ( $octets[0] === '192' && $octets[1] === '168' )
            || $octets[0] === '127';

    }

    /**
     * @param $value
     * @param $equals
     *
     * @return bool
     */
    public static function constant_is_defined_and_equals ( $value, $equals = true ) {

        return defined ( $value ) && constant ( $value ) === $equals;
    }
}
