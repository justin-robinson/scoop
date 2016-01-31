<?php

// parse script options
$opts = getopt (
    "",
    [
        "site:",
        "no-db"
    ] );

// set site name if one was provided
if ( array_key_exists ( 'site', $opts ) ) {
    $_SERVER['R_SITE_NAME'] = $opts['site'];
}

if ( isset($opts['no-db'])) {
    define('NO_DB_CONNECT', true);
}

// get the base
require_once dirname ( __FILE__ ) . "/../base.php";

return $opts;