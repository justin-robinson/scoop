<?php

require_once dirname(__FILE__) . '/../phpr/commandline.php';

// parse script options
$args = phpr\CommandLine::parseArgs( $_SERVER['argv']);

// set site name if one was provided
if ( array_key_exists ( 'site', $args ) ) {
    $_SERVER['R_SITE_NAME'] = $args['site'];
}

// get the base
require_once dirname ( __FILE__ ) . "/../base.php";

return $args;