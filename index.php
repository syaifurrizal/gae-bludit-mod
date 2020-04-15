<?php

/*
 * Bludit
 * https://www.bludit.com
 * Author Diego Najar
 * Bludit is opensource software licensed under the MIT license.
*/

//use Google\Cloud\Storage\StorageClient;
require 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient; 
$storage = new StorageClient();
$storage->registerStreamWrapper();

function get_real_file_to_load($full_request_uri)
{
    $request_uri = @parse_url($full_request_uri)['path'];

    // Load the file requested if it exists
    if (is_file(__DIR__ . $request_uri)) {
        return $request_uri;
    }

    // Send everything else through index.php
    return '/index.php';
}

$project_id = $_SERVER['GOOGLE_CLOUD_PROJECT'];
$bucket_name = 'gs://'.$project_id.'.appspot.com/';
//$bucket_name = 'gs://bludit.appspot.com/';

//echo $project_id;
//echo $bucket_name;

// Check if Bludit is installed
if (!file_exists($bucket_name.'bl-content/databases/site.php')) {
    //$base = dirname($_SERVER['SCRIPT_NAME']);
    $base = get_real_file_to_load($_SERVER['REQUEST_URI']);
    $_SERVER['SCRIPT_FILENAME'] = $_ENV['SCRIPT_FILENAME'] = __DIR__ . $base;
	//$base = rtrim($base, '/');
	//$base = rtrim($base, '\\'); // Workaround for Windows Servers
    //header('Location:'.$base.'/install.php');
    require_once(__DIR__.'/install.php');
    //header('Location:/install.php');
	exit('<a href="./install.php">Install Bludit first.</a>');
}

// Load time init
$loadTime = microtime(true);

// Security constant
define('BLUDIT', true);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// PHP paths for init
define('PATH_ROOT', __DIR__.DS);
define('PATH_BOOT', PATH_ROOT.'bl-kernel'.DS.'boot'.DS);

// Init
require(PATH_BOOT.'init.php');

// Admin area
if ($url->whereAmI()==='admin') {
	require(PATH_BOOT.'admin.php');
}
// Site
else {
	require(PATH_BOOT.'site.php');
}
