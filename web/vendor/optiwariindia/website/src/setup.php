<?php
namespace optiwariindia\website;

class setup{
    private static function init(){
        view::dir(__DIR__.DIRECTORY_SEPARATOR."setup");
        view::init();
    }
    public static function terms(){
        self::init();
        view::render("terms.twig",[]);
    }
    public static function database(){
        self::init();
        view::render("database.twig",[]);
    }
    public static function mail(){
        self::init();
        view::render("mail.twig",[]);
    }
}