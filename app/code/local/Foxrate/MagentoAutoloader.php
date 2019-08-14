<?php

require_once('lib/Zend/Loader.php');

//instantiate a zend autoloader first, since we
//won't be able to do it in an unautoloader universe
$autoLoader = Zend_Loader_Autoloader::getInstance();


$autoloader_callbacks = spl_autoload_functions();
$original_autoload=null;
foreach($autoloader_callbacks as $callback)
{
    if(is_array($callback) && $callback[0] instanceof Varien_Autoload)
    {
        $original_autoload = $callback;
    }
}

spl_autoload_unregister($original_autoload);

$autoLoader->pushAutoloader(array('ezcBase', 'autoload'), 'ezc');

return $original_autoload;