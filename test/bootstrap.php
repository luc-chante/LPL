<?php
error_reporting(0xffff);

define("DS", DIRECTORY_SEPARATOR);
define("PS", PATH_SEPARATOR);

set_include_path(get_include_path() . PS . str_replace("test", "src", __DIR__));

spl_autoload_register(function($class_name) {
    $path = str_replace("\\", DS, $class_name) . ".php";
    if (stream_resolve_include_path($path)) {
        require_once $path;
    }
});