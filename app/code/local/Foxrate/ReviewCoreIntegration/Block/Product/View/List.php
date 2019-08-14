<?php


class Foxrate_ReviewCoreIntegration_Block_Product_View_List extends Mage_Review_Block_Product_View_List
{

    protected $config;

    protected $reviewModel;

    public function __construct()
    {
        parent::__construct();

        $this->config = Mage::getModel('reviewcoreintegration/config');
    }

    protected function _toHtml()
    {
        $this->assign('foxrateReview', $this->getReviewModel());

        return parent::_toHtml();
    }

    /**
     * Get module url
     *
     * @param $module
     * @param $image
     * @return string
     */
    public function getModuleUrl($module, $image) {

        return $this->getSkinUrl('images/foxrate/' . $image);

    }


    /**
     * Check if rich snippet is active
     *
     * @return mixed
     */
    public function richSnippetIsActive()
    {
        return $this->config->getConfigParam('foxrateRichSnippetActive');
    }


    /**
     * Create review sorting criteria
     * @return array
     */
    public function getSortingCriteria()
    {
        $pageNav = $this->getReviewModel()->getSortingCriteria();
        return $pageNav;
    }

    public function getAjaxControllerUrl()
    {
        return Mage::getModel('reviewcoreintegration/config')->getAjaxControllerUrl();
    }

    /**
     * Gets Current Shop url
     */
    public function getFoxrateShopUrl()
    {
        return Mage::getModel('reviewcoreintegration/config')->getShopUrl();
    }

    /**
     * Return currently shown product id
     * @return mixed
     */
    public function getFoxrateProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * Lazy loader for review model
     */
    public function getReviewModel()
    {
        if (null == $this->reviewModel)
        {
            $this->reviewModel = Mage::getModel('reviewcoreintegration/review');
        }
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

    public function getReviewTotalData($entityId)
    {
        return Mage::getModel('reviewcoreintegration/reviewtotals')->getReviewTotalData($entityId);
    }

}