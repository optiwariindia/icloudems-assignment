<?php

namespace icloudems\assignment;

class module extends \optiwariindia\website\module
{
    public static function getDBConfig()
    {
        if (!file_exists(PROJECTROOT . "/db.json")) controller::redirect("/setup/db");
        return json_decode(file_get_contents(PROJECTROOT . "/db.json"), 1);
    }
    public static function init()
    {
        $dbConfig = self::getDBConfig();
        if (!$dbConfig)
            return parent::init();
        return new module($dbConfig);
    }
    public static function upload()
    {
        $req = request::inputs();
        $file = $req["datafile"]["tmp_name"];
        return $file;
    }
    public static function getFileEncoding($file)
    {
        $content = file_get_contents($file);
        if ($content == false) return false;
        return mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'ASCII', 'Windows-1252'], true);
        
    }
    public static function convertToUTF8($file){
        $inputFile=$file;
        $temp=explode(DIRECTORY_SEPARATOR,$file);
        $temp[(count($temp) - 1)]="out-".$temp[(count($temp) - 1)];
        $outputFile=implode(DIRECTORY_SEPARATOR,$temp);
        if (!file_exists($inputFile)) {
            die("File not found: $inputFile");
        }
        file_put_contents($outputFile,mb_convert_encoding(file_get_contents($inputFile), 'UTF-8', self::getFileEncoding($inputFile)));
        return $outputFile;
    }
}
