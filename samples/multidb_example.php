<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', -1);
ini_set('display_errors', 1);

$root = realpath(dirname(dirname(__FILE__)));
$library = "$root/src";

$path = array($library, get_include_path());
set_include_path(implode(PATH_SEPARATOR, $path));

require_once 'Application/MultiDb.php';

unset($root, $library, $path);

