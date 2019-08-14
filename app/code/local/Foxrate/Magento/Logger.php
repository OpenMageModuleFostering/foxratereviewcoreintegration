<?php

class Foxrate_Magento_Logger {

    public function log($message)
    {
        Mage::log($message);
    }

    public function logException($message)
    {
        Mage::logException($message);
    }
}