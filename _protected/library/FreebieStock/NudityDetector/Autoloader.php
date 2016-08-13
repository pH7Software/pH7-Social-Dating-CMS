<?php

/**
* @author FreebieVectors.com
*
* Class for autoloading other classes
*/
class Autoloader {

    public static $dirs = array();

    public static function addDirectory($dir) {
        array_push(Autoloader::$dirs, $dir . '/');
    }

    public static function register() {
        spl_autoload_register(array('self', 'load'));
    }

    public static function classFileExists($class, $dir_index = 0) {
        $file = self::$dirs[$dir_index] . str_replace('_', '/', $class) . '.php';
        if(file_exists($file)) return $file;
        return false;
    }

    /*
     * Auto class loader, example:
     * loads MyClass_Adapter_Name from MyClass/Adapter/Name.php
     */
    public static function load($name){
        $cnt = count(self::$dirs);
        for($i = 0; $i < $cnt; $i++)
            if($file = self::classFileExists($name, $i)) {
                require_once $file;
                return;
            }
    }

}

Autoloader::addDirectory(__DIR__);
Autoloader::register();