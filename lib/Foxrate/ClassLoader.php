<?php
/**
 * Class Foxrate_Sdk_Api_Autoloader
 */
class Foxrate_Sdk_Api_Autoloader
{
    /**
     * Spl autoload register
     */
    public function register()
    {
        spl_autoload_register(array($this,'loadClass'));
    }

    /**
     * Class loader
     * @param $className
     */
    public function loadClass($className)
    {
        //ensure to load only current library scope
        if (stripos($className, basename(dirname(__FILE__)) . '_') !== 0) {
            return;
        }

        $className = ltrim($className, '\\');
        $fileName  = '';

        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $classPath = str_replace('_', DIRECTORY_SEPARATOR, $className);
        $fileName = $classPath . '.php';

        require_once FOXRATE_BASEPATH . $fileName;
    }

}

