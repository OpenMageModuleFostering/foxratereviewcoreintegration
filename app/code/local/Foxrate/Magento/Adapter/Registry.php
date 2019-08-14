<?php

class Foxrate_Magento_Adapter_Registry
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