<?

class Database_Connection {

    // the mysqli resource
    private static $mysqli;

    // call this to initiate a db connection
    static function connect() {

        // configs file is here
        $configFilepath = $_SERVER['DOCUMENT_ROOT'] . '/configs/db.json';

        // open file
        $config = file_get_contents($configFilepath);

        // did we get the file?
        if ( $config ) {

            // decode the json
            $config = json_decode($config);

            // attempt to connect to the db
            self::$mysqli = new mysqli(
                $config->host,
                $config->user,
                $config->password,
                null);

            // die on error
            if ( self::$mysqli->connect_error ) {
                die( 'Connect Error (' . self::$mysqli->connect_errno . ') '
                    . self::$mysqli->connect_error);
            }

        } else {
            throw new Error('failed to open db credentials file at ' . $configFilepath);
        }

    }

    // call this to close the connection
    static function disconnect () {

        if ( ! self::close() ) {
            die( 'Error closing connection' );
        }
    }

    // pass all missing static function calls the $mysqli resource
    public static function __callStatic( $name, $arguments ) {

        // does the unimplemented function exist on the mysqli resource?
        if ( method_exists( self::$mysqli, $name ) ) {

            // well call it!
            return call_user_func_array(
                array(
                    self::$mysqli,
                    $name
                ),
                $arguments);
        }

        // how about a property on the mysqli resource?
        if ( isset(self::$mysqli->$name) ) {
            return self::$mysqli->$name;
        }
    }
}
?>