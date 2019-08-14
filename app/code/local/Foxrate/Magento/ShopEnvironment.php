<?php

/**
 * class FoxrateApiMagento_ShopEnvironment
 * This class retrieves a specific metadata for Foxrate from current shop
 */
class Foxrate_Magento_ShopEnvironment
    extends  Foxrate_Sdk_ApiBundle_Entity_AbstractShopEnvironment
    implements Foxrate_Sdk_ApiBundle_Entity_ShopEnvironmentInterface
{

    const BRIDGE_URI  = 'foxrate_api';

    private $pluginVersion = '3.5.1';

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
        return Mage::getConfig()->getNode()->modules->Foxrate_ReviewCoreIntegration->version;
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
        list($locale, $part2) = explode('_',  Mage::app()->getLocale()->getLocaleCode());
        return $locale;
    }

    public function getShopName()
    {
        return Mage::getStoreConfig('general/store_information/name');
    }

}
