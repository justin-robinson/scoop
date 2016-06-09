<?php

namespace Scoop;

/**
 * Class File
 * @package Scoop
 */
class File {

    /**
     * @param $dirname
     *
     * @return bool
     */
    public static function delete_directory ( $dirname ) : bool {

        if( !file_exists( $dirname ) ) {
            return true;
        }

        if( !is_dir( $dirname ) ) {
            return unlink( $dirname );
        }

        foreach ( scandir( $dirname ) as $item ) {
            if( $item == '.' || $item == '..' ) {
                continue;
            }

            if( !self::delete_directory( $dirname . DIRECTORY_SEPARATOR . $item ) ) {
                return false;
            }

        }

        return rmdir( $dirname );

    }
}
