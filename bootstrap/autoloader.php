<?php

require_once \Scoop\Config::get_option ( 'install_dir' ) . '/scoop/path.php';

/**************
 * composer
 * ************/
$composerAutoloaderPath = \Scoop\Config::get_option ( 'install_dir' ) . '/vendor/autoload.php';
if ( file_exists ( $composerAutoloaderPath ) ) {
    require_once $composerAutoloaderPath;

    $run     = new Whoops\Run;
    $handler = new Whoops\Handler\PrettyPageHandler;

    // Set the title of the error page:
    $handler->setPageTitle("Whoops! There was a problem.");

    $run->pushHandler($handler);

    // Add a special handler to deal with AJAX requests with an
    // equally-informative JSON response. Since this handler is
    // first in the stack, it will be executed before the error
    // page handler, and will have a chance to decide if anything
    // needs to be done.
    if (Whoops\Util\Misc::isAjaxRequest()) {
        $run->pushHandler(new Whoops\Handler\JsonResponseHandler);
    }

    // Register the handler with PHP, and you're set!
    $run->register();
}

/**************
 * Scoop
 **************/

// load all classpaths that exist
foreach ( \Scoop\Config::get_class_paths () as $classPath ) {
    if ( $classPath = realpath ( $classPath ) ) {
        set_include_path ( get_include_path () . PATH_SEPARATOR . $classPath . '/' );
    }
}

/**
 * 'spl_autoload' | use built-in psr-0 spl_autoload()
 * true           | error thrown if 'spl_autoload' not found
 * true           | prepend this autoloader to the beginning of the autoload queue
 */
spl_autoload_register ( 'spl_autoload', true, true );
