<?php

if (file_exists($_SERVER['SCRIPT_FILENAME']) && strtolower(substr($_SERVER['SCRIPT_NAME'],-4)) !== '.php') {
    $filename = $_SERVER['SCRIPT_FILENAME'];

    $expires = 60 * 5;
    header("Pragma: public");
    header("Cache-Control: maxage=" . $expires);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mimeMap = [
        'js'   => 'text/javascript',
        'css'  => 'text/css',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'svg'   => 'image/svg+xml',
    ];
    if (isset($mimeMap[$ext])) {
        $mime = $mimeMap[$ext];
    } else {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_SERVER['SCRIPT_FILENAME']);
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