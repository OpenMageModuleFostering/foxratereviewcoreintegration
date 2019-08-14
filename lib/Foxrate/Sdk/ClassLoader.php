<?php

class Foxrate_Sdk_ClassLoader
{

    const FOXRATE_SDK = 'Foxrate_Sdk_';

    /**
     * Spl autoload register
     */
    public static function register($autoLoaders = false)
    {
        $registerAutoload = false;

        if (!$autoLoaders) {
            $autoLoaders = spl_autoload_functions();
        }

        if (count($autoLoaders) == 1
            && ($autoLoaders[0] === '__autoload' || @get_class($autoLoaders[0][0]) == 'Composer\Autoload\ClassLoader')) {
            $registerAutoload = true;
        }

        spl_autoload_register(array('static', 'loadClass'));

        if ($registerAutoload) {
            foreach ($autoLoaders as $func) {
                spl_autoload_register($func);
            }
        }
    }

    /**
     * Class loader
     * @param $className
     */
    public static function loadClass($className)
    {
        //ensure to load only SDK
        if (!stristr($className, self::FOXRATE_SDK)) {
            return;
        } else {
            $className = str_replace(self::FOXRATE_SDK, "", $className);
        }

        $className = ltrim($className, '\\');
        $classPath = str_replace('_', DIRECTORY_SEPARATOR, $className);
        $fileName = $classPath . '.php';

        require_once __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    }

    public static function isSdk($className)
    {
        return stristr($className, self::FOXRATE_SDK);
    }
}
