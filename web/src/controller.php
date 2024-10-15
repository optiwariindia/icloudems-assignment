<?php
namespace icloudems\assignment;
class controller{
    public static function load(){
        // Setting up view engine
        view::dir(PROJECTROOT."/views");
        view::init();
        $method=request::method();
        $module=request::url();
        if(($module[0]??"")=="")$module[0]="controller";
        if(($module[1]??"")=="")$module[1]="index";
        if(method_exists("icloudems\\assignment\\".$module[0],$module[1])){
            call_user_func_array(["icloudems\\assignment\\".$module[0],$module[1]],[]);
        }
        else{
            view::render("404.twig",[
                "status"=>"error",
                "message"=>"Requested service is not available at the moment."
            ]);
        }
    }
    public static function index(){
        if(!file_exists(PROJECTROOT."/db.json"))self::redirect("/setup/db");
        self::redirect("/setup/schema");
    }
    public static function redirect($path){
        $url=request::url();
        if($path=="/".implode(DIRECTORY_SEPARATOR,$url))return;
        header("Location: {$path}");
    }
}