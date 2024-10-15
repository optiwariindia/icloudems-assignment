<?php
// error_reporting(false);
# phpinfo();die;
# Setting up include path
$parent = call_user_func(function () {
    $temp = explode(PATH_SEPARATOR, get_include_path());
    $temp[] = __DIR__;
    $pdir = explode(DIRECTORY_SEPARATOR, __DIR__);
    array_pop($pdir);
    $temp[] = implode(DIRECTORY_SEPARATOR, $pdir);
    set_include_path(implode(PATH_SEPARATOR, $temp));
    return implode(DIRECTORY_SEPARATOR, $pdir);
});
define("PROJECTROOT", $parent);
include "vendor/autoload.php";
use icloudems\assignment\controller;
controller::load();