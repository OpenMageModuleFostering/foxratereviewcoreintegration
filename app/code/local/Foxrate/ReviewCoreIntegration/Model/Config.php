<?php

class Foxrate_ReviewCoreIntegration_Model_Config extends Mage_Core_Helper_Abstract
{

    protected static $configKeys = array(
        //order export group
        //----------------------------
        'foxrateUsername' =>
        'ordersexport/foxrateOrdersExport/fox_api_username',
        'foxratePassword' =>
        'ordersexport/foxrateOrdersExport/fox_api_password',

        //review cre integration group
        'foxratePR_WriteReview' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/write_review',
        'foxratePR_RevsPerPage' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/reviews_per_page',
        'foxratePR_SortBy' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/sort_by',
        'foxratePR_SortOrder' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/sort_order',
        'foxratePR_Summary' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/summary',
        'foxratePR_OrderRichSnippet' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/order_rich_snippet',
        'foxratePR_CatalogDisplay' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/catalog_display',
        'foxratePR_CatalogTooltip' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/catalog_tooltip',
        'foxratePR_Page' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/page',
        'foxrate_lastProductReviewImport' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/last_product_review_import',
        'foxrateSellerId' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/seller_id',
        'foxrateShopId' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/shop_id',
        'foxrateOverrideShopId' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/shop_id_overide',
        'foxrateRichSnippetActive' =>
        'ordersexport/foxrateReviewCoreIntegrationConf/rich_snippet_active',
    );

    public function writeToLog($message)
    {
        Mage::log($message, null, 'foxrateImporte.log');
        return $message;
    }

    public function saveRegistryVar($type = null, $name, $var)
    {

        Mage::register($name, $var, true);
    }

    public function getRegistryVar($name)
    {
        return Mage::registry($name);
    }

    public function saveShopConfVar($type = null, $name, $var)
    {

        $switch = new Mage_Core_Model_Config();
        $switch ->saveConfig($this->getConfigKey($name), $var, 'default', 0);
    }

    public function getShopConfVar($name)
    {
        return Mage::getStoreConfig(
            $name,
            Mage::app()->getStore()
        );
    }

    public function getConfigKeys()
    {
        return self::$configKeys;
    }

    public function getConfigKey($val)
    {
        $config  = $this->getConfigKeys();

        if (!isset($config[$val]))
        {
            throw Exeption ('Config variable ' . $val . '  not found!');
        }

        return $config[$val];
    }


    public function getConfigParam($name)
    {

        if (array_key_exists($name, $this->getConfigKeys()))
        {
            return Mage::getStoreConfig(
                $this->getConfigKey($name),
                Mage::app()->getStore()
            );
        }

        $config = array(
            'sCompileDir' => Mage::getBaseDir('var') . '/cache/',
        );

        if (isset($config[$name]))
        {
            return $config[$name];
        }

        Mage::log('Config variable ' . $name . '  not found!');
        // Mage::registry($name);
    }

    public function getShopUrl()
    {
        return Mage::helper('core/url')->getHomeUrl();
    }

    public function getLanguageAbbr()
    {
        // strtolower(Mage::getStoreConfig('general/country/default'));

        list($locale, $part2) = explode('_',  Mage::app()->getLocale()->getLocaleCode());
        return $locale;
    }

    public function getLogsDir()
    {
        return Mage::getBaseDir('var') . '/log';
    }

    public function getAjaxControllerUrl()
    {
        return Mage::getUrl('foxratereviews');
    }

}
