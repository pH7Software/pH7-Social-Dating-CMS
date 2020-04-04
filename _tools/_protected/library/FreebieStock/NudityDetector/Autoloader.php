<?php

/**
 * @author FreebieVectors.com
 *
 * Modified by Pierre-Henry Soria <hello@ph7cms.com>
 *
 * Class for autoloading other classes
 */
class Autoloader
{
    /** @var array */
    private static $dirs = [];

    /**
     * @param string $dir
     */
    public static function addDirectory($dir)
    {
        array_push(Autoloader::$dirs, $dir . '/');
    }

    public static function register()
    {
        spl_autoload_register(['self', 'load']);
    }

    /**
     * @param string $class
     * @param int $dir_index
     *
     * @return bool|string
     */
    public static function classFileExists($class, $dir_index = 0)
    {
        $file = self::$dirs[$dir_index] . str_replace('_', '/', $class) . '.php';

        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public static function load($name)
    {
        $cnt = count(self::$dirs);

        for ($i = 0; $i < $cnt; $i++)
            if ($file = self::classFileExists($name, $i)) {
                require_once $file;
                return;
            }
    }
}

Autoloader::addDirectory(__DIR__);
Autoloader::register();
