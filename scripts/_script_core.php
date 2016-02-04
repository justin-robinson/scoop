<?php

require_once dirname ( __FILE__ ) . '/../scoop/commandline.php';

// parse script options
$args = Scoop\CommandLine::parseArgs ( $_SERVER['argv'] );

// set site name if one was provided
if ( array_key_exists ( 'site', $args ) ) {
    $_SERVER['SITE_NAME'] = $args['site'];
}

// get the base
require_once dirname ( __FILE__ ) . "/../bootstrap.php";

return $args;
