<?php
namespace optiwariindia\website;

class view{
    private static $view;
    private static $twig;
    public static function dir($dir){
        self::$view=$dir;
    }
    public static function init(){
        if(!isset(self::$view)){
            return false;
        }
        if(isset(self::$twig))return true;
        $loader = new \Twig\Loader\FilesystemLoader(self::$view);
        $twig = new \Twig\Environment($loader, ['debug' => true]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        self::$twig=$twig;
        return true;
    }
    public static function render($file,$data,$errorCode=""){
        if($errorCode!="")http_response_code($errorCode);
        if(!isset(self::$twig))return false;
        echo self::$twig->render($file,$data);
        die;
    }
    public static function api($data){
        echo json_encode($data);die;
    }
    public static function get($file,$data){
        if(!isset(self::$twig))return false;
        return self::$twig->render($file,$data);
    }
}