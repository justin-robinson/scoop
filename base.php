<?

$installDirectory = pathinfo(__FILE__)['dirname'];

$_SERVER['R_DOCUMENT_ROOT'] = $installDirectory;

$frameworkConfig = json_decode(file_get_contents( $_SERVER['R_DOCUMENT_ROOT'] . '/configs/framework.json'));

foreach ( $frameworkConfig as $option => $value ) {
    $_SERVER['R_' . $option] = $value;
}

// the autoloader
require_once($_SERVER['R_DOCUMENT_ROOT'] . '/autoloader.php');

// connect to mysql server
\Database\Connection::connect();

function serverError($message) {
    header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500);
    echo $message;
    die;
}

// makes sql safe
function r3a($sqlString, $quoteChar = "'") {
    $sqlString = \Database\Connection::real_escape_string($sqlString);
    return $quoteChar . addslashes($sqlString) . $quoteChar;
}

// handles values to sql strings
function print_sql($value, $quoteChar = "'"){
    if ( is_null($value) ) {
        $sqlValue = 'NULL';
    } else if ( is_object($value) && is_a($value, 'Database_Literal') ) {
        $sqlValue = (string)$value;
    } else {
        $sqlValue = r3a($value, $quoteChar);
    }

    return $sqlValue;
}

function r3a_array( &$array, $quoteChar ) {

    foreach ( $array as $index => &$value ) {

        $value = print_sql($value, $quoteChar);

    }

}