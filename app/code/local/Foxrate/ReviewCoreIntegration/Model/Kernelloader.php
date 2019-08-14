<?php


class Foxrate_ReviewCoreIntegration_Model_Kernelloader
{
    public function getKernel()
    {
        return self::getSingleton('Foxrate_Kernel');
    }

    public static function getSingleton($modelClass='', array $arguments=array())
    {
        $registryKey = '_singleton/'.$modelClass;
        if (!Mage::registry($registryKey)) {
            $kernel = Foxrate_Kernel::getInstance();
            $kernel->boot();
            Mage::register($registryKey, $kernel);
        }
        return Mage::registry($registryKey);
    }
} 