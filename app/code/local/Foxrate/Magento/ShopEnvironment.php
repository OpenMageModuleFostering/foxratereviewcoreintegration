<?php

/**
 * class FoxrateApiMagento_ShopEnvironment
 * This class retrieves a specific metadata for Foxrate from current shop
 */
class Foxrate_Magento_ShopEnvironment
    extends  Foxrate_Sdk_Api_Entity_AbstractShopEnvironment
    implements Foxrate_Sdk_Api_Entity_ShopEnvironmentInterface
{

    const BRIDGE_URI  = 'foxrate_api';

    private $pluginVersion = '3.3.3';

    /**
     * Returns the particular shop system version.
     *
     * @return string
     */
    public function shopSystem()
    {
        return 'Magento';
    }

    /**
     * Returns the particular shop system version.
     *
     * @return string
     */
    public function shopSystemVersion()
    {
        $version = Mage::getVersion();
        if(method_exists('Mage', 'getEdition')) {
            $version .= ' ' . Mage::getEdition();
        }

        return $version;
    }

    /**
     * Returns particular plugin implementation version.
     *
     * @return mixed
     */
    public function pluginVersion()
    {
        return $this->pluginVersion;
    }

    /**
     * Get bridge url - special url for Foxrate Api to access shop module Api
     * @return string
     */
    public function bridgeUrl()
    {
        return Mage::getUrl('reviewcoreintegration_export/index/export');
    }

    public function getShopLanguage()
    {
        return 'en';
    }

}
