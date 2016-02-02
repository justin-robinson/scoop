<?php

namespace phpr;

/**
 * Class Server
 * @package phpr
 */
class Server {


    /**
     * @param $message
     *
     * @todo find a better place for this
     */
    public static function send_error ( $message ) {

        header ( $_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500 );
        echo $message;
        die;
    }
}
