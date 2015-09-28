<?

function r_autoloader( $class ) {

    // class names can be uppercase but files are lower case
    $class = strtolower($class);

    // an array containing each folder to get to the file
    $foldersArray = explode('_', $class);

    // last folder is just the file name so we can kill it
    array_pop($foldersArray);

    // glue folders together
    $folders = implode('/', $foldersArray);

    // full path to the file
    $butt = '/' . $folders . '/' . $class . '.php';
    $filepath = $_SERVER['DOCUMENT_ROOT'] . $butt;

    // check in project folder first, then global folder
    if ( file_exists($filepath) ) {
        include_once( $filepath );
    } else {

        $filepath = $_SERVER['R_DOCUMENT_ROOT'] . $butt;

        if ( file_exists($filepath) ) {
            include_once($filepath);
        } else {
            throw new Error( "Class : '{$class}', was not found at '{$filepath}'");
        }

    }


}

spl_autoload_register('r_autoloader');