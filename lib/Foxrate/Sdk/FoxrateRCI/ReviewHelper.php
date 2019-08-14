<?php

class Foxrate_Sdk_FoxrateRCI_ReviewHelper
{

    protected $reviewModel;

    protected $productPage;

    function __construct($reviewModel, $config)
    {
        $this->reviewModel = $reviewModel;
        $this->config = $config;
    }

    /**
     * Lazy loader for review model
     */
    public function getReviewModel()
    {
        return $this->reviewModel;
    }

    /**
     * Extracts date from specific format
     * @param $date
     * @return mixed
     */
    public function calcReviewDate($date)
    {
        return $this->getReviewModel()->calcReviewDate($date);
    }

    public function getAjaxControllerUrl()
    {
        return $this->config->getAjaxControllerUrl();
    }

    /**
     * Gets Current Shop url
     */
    public function getFoxrateShopUrl()
    {
        return __PS_BASE_URI__;
    }

    /**
     * Gets Current Shop url
     */
    public function getFoxrateProductId()
    {
        return (int)Tools::getValue('id_product');
    }

    public function getTitle()
    {
        return (int)Tools::getValue('name');
    }
    
}