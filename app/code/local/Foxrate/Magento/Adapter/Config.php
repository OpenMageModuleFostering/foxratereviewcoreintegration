<?php

class Foxrate_Magento_Adapter_Config extends Mage_Core_Model_Config_Data
{

    /**
     * This is shop specific global variable.
     * @var
     */
    protected $context;

    /**
     * Full Path to cached product reviews directory
     * @var string
     */
    protected $sCachedProdRevsDir = "";

    /**
     * Subdirectory for Foxrate's cached files of product reviews
     * @var string
     */
    protected $sFoxrateCachedProductsSubDir = "ProductReviews";

    /**
     * Subdirectory for Foxrate's cached files temporary directory
     * Fail-safe way to download files first, and then move them to permanent directory
     * If download fails, permanent directory (old) files will be used instead
     * @var string
     */
    protected $sFoxrateCachedProductsTemp = "tmp";

    /**
     * Directory name for main cached files of Foxrate
     * @var string
     */
    protected $sFoxrateCachedFilesDir = "FoxrateCache";

    protected static $configKeys = array(
        //order export group
        //----------------------------
        'foxrateUsername' =>
            'reviewcoreintegration/foxrateReviewCoreIntegration/fox_api_username',
        'foxratePassword' =>
            'reviewcoreintegration/foxrateReviewCoreIntegration/fox_api_password',

        //review cre integration group
        'foxratePR_WriteReview' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/write_review',
        'foxratePR_RevsPerPage' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/reviews_per_page',
        'foxratePR_SortBy' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/sort_by',
        'foxratePR_SortOrder' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/sort_order',
        'foxratePR_Summary' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/summary',
        'foxratePR_OrderRichSnippet' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/rich_snippet_active',
        'foxratePR_CatalogDisplay' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/catalog_display',
        'foxratePR_CatalogTooltip' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/catalog_tooltip',
        'foxratePR_Page' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/page',
        'foxrate_lastProductReviewImport' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/last_product_review_import',
        'foxrateSellerId' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/seller_id',
        'foxrateShopId' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/shop_id',
        'foxrateOverrideShopId' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/shop_id_overide',
        'foxrateRichSnippetActive' =>
            'reviewcoreintegration/foxrateReviewCoreIntegrationConf/rich_snippet_active',
    );

    public function writeToLog($message)
    {
        Mage::log($message);
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

    public function saveShopConfVar($name, $var, $type = null)
    {
        Mage::getModel('core/config')->saveConfig($this->getConfigKey($name), $var );
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
        $config = $this->getConfigKeys();

        if (!isset($config[$val])) {
            throw new Exception ('Config variable ' . $val . '  not found!');
        }

        return $config[$val];
    }

    /**
     * @param $name
     * @return null
     */
    public function getConfigParam($name)
    {
        $configParam = $this->getFieldsetDataValue($name);
        if ($configParam !== null) {
            return $configParam;
        }

        if (array_key_exists($name, $this->getConfigKeys())) {
            $value = $this->getShopConfVar($this->getConfigKey($name));

            //if $value = false, as this is default value for not excisting values, lets return null.
            return $value ? $value : null;
        }

        $config = array(
            'sCompileDir' => $this->defaultCachePath(),
        );

        if (isset($config[$name])) {
            return $config[$name];
        }

        $this->writeToLog('Config variable ' . $name . '  not found!');
    }

    public function getShopUrl()
    {
        return Mage::helper('core/url')->getHomeUrl();
    }

    public function getLanguageAbbr()
    {
        list($locale, $part2) = explode('_',  Mage::app()->getLocale()->getLocaleCode());
        return $locale;
    }

    public function getModuleUrl($file)
    {
        return Mage::getDesign()->getSkinUrl($file);
    }

    public function getHomeUrl()
    {
        return $this->getShopUrl() ;
    }

    public function getLogsDir()
    {
        return Mage::getBaseDir('var') . '/log';
    }

    public function getAjaxControllerUrl()
    {
        return Mage::getUrl('foxratereviews');
    }

    public function defaultCachePath()
    {
        return Mage::getBaseDir('var') . '/cache/';
    }

    public function getTempFilesPath()
    {
        return $this->getConfigParam("sCompileDir");
    }

    public function getCachedReviewsPath()
    {
        return $this->getTempFilesPath() . $this->sFoxrateCachedFilesDir . "/" . $this->sFoxrateCachedProductsSubDir;
    }

    public function getCachedReviewsPathTemp()
    {
        return $this->getCachedReviewsPath() . "/" . $this->sFoxrateCachedProductsTemp;
    }

}
