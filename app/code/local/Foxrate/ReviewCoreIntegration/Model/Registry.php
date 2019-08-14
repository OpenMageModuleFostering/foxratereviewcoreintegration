<?php

class Foxrate_ReviewCoreIntegration_Model_Registry extends Mage_Core_Helper_Abstract
{
    /**
     * Returns OxConfig instance
     *
     * @static
     *
     * @return OxConfig
     */
    public function getConfig()
    {
        return $this->helper('reviewcoreintegration/config');
    }


}