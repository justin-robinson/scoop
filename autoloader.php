<?

function r_autoloader( $fullClassPath ) {

    // class names can be uppercase but files are lower case
    $fullClassPath = strtolower($fullClassPath);

    // an array containing each folder to get to the file
    $foldersArray = explode('\\', $fullClassPath);

    // last item is the file and class name
    $className = array_pop($foldersArray);

    // glue folders together
    $folders = implode('/', $foldersArray);

    // full path to the file
    $butt = '/' . $folders . '/' . $className . '.php';
    $filepath = $_SERVER['R_DOCUMENT_ROOT'] . $butt;

    // check in project folder first, then global folder
    if ( file_exists($filepath) ) {
        include_once( $filepath );
    } else {

        $filepath = $_SERVER['R_DOCUMENT_ROOT'] . $butt;

        if ( file_exists($filepath) ) {
            include_once($filepath);
        } else {
            throw new Error( "Class : '{$fullClassPath}', was not found at '{$filepath}'");
        }

    }


}

spl_autoload_register('r_autoloader');