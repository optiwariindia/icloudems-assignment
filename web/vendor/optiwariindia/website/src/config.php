<?php

namespace optiwariindia\website;

class config
{
    private static $config;

    public static function file()
    {

        $dir = constant("HOMEDIR");
        if ($dir == null)
            $dir = $_SERVER['DOCUMENT_ROOT'];
        $cfg = constant("CONFIG");
        if ($cfg == null)
            $cfg = "config.json";
        return $dir . DIRECTORY_SEPARATOR . $cfg;
    }
    public static function installed()
    {
        return file_exists(self::file());
    }
    public static function init()
    {
        if (!self::installed()) return false;
        $temp = file_get_contents(self::file());
        self::$config = json_decode($temp, 1);
    }
}
