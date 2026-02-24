<?php

if (file_exists($_SERVER['SCRIPT_FILENAME']) && strtolower(substr($_SERVER['SCRIPT_NAME'],-4)) !== '.php') {
    $filename = $_SERVER['SCRIPT_FILENAME'];

    $expires = 60 * 5;
    header("Pragma: public");
    header("Cache-Control: maxage=" . $expires);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    switch ($ext) {
        case 'js':
            $mime = 'text/javascript';
            break;
        case 'css':
            $mime = 'text/css';
            break;
        case 'woff2':
            $mime = 'font/woff2';
            break;
        case 'woff':
            $mime = 'font/woff';
            break;
        case 'ttf':
            $mime = 'font/ttf';
            break;
        default:
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $filename);
            finfo_close($finfo);
    }
    header('Content-type: ' . $mime);

    echo file_get_contents($_SERVER['SCRIPT_FILENAME']);
    die;  // serve the requested resource as-is.
} else if($_SERVER['SCRIPT_NAME'] === '/index.php') {
    chdir($_SERVER['DOCUMENT_ROOT']);
    include_once 'index.php';
}else{
    return false;
}


