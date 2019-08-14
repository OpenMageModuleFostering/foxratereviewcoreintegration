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

    private $pluginVersion = '3.5.90';

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
        return Mage::getUrl(
            'reviewcoreintegration_export/index/export',
            array(
                '_store' => $this->getStoreId(),
                '_store_to_url' => true
            )
        );
    }

    /**
     * @return int
     */
    protected function getStoreId() {

        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
        {
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        }
        elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
        {
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        }
        else // default level
        {
            $store_id = 0;
        }

        return $store_id;
    }

    public function getShopLanguage()
    {
        list($locale, $part2) = explode('_',  Mage::app()->getLocale()->getLocaleCode());
        return $locale;
    }

    public function getShopName()
    {
        if ($this->isTestEnvironment()) {
            return "Foxrate Test Shop";
        }

        $storeInformationName = Mage::getStoreConfig('general/store_information/name');
        if (!empty($storeInformationName)) {
            return $storeInformationName;
        }

        $frontendName = Mage::app()->getStore($this->getStoreId())->getFrontendName();
        return $frontendName . $this->getViewName();
    }

    private function getViewName()
    {
        $store = Mage::app()->getStore($this->getStoreId());
        $viewName =  $store->getName();

        //don' add empty and Admin as viewnames
        return ((!empty($viewName) and $viewName != "Admin") ? " - " . $viewName : "");
    }

    private function isTestEnvironment()
    {
        return preg_match('/magento-php52-[\d]*.vm/', $_SERVER['HTTP_HOST']);
    }
}
