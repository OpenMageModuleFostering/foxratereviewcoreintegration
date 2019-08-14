<?php


class Foxrate_ReviewCoreIntegration_Block_Product_View_List extends Mage_Review_Block_Product_View_List
{

    protected $config;

    protected $reviewModel;

    public function __construct()
    {
        parent::__construct();

        $this->config = $this->getConfig();
    }

    protected function _toHtml()
    {
        $productid = $this->getFoxrateProductId();
        $processReviews = $this->getKernel()->get('rci.process_reviews');

        $this->assign('foxrateReview', $this->getKernel()->get('rci.review'));
        $this->assign('foxrateProductReviews', $processReviews->getRawProductReviews($productid));
        $this->assign('pages', $processReviews->getPageNav());
        $this->assign('foxrateReviewGeneralData', $this->getKernel()->get('rci.review_totals')->getReviewTotalData($productid));

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

    public function getAjaxControllerUrl()
    {
        return $this->getConfig()->getAjaxControllerUrl();
    }

    /**
     * Gets Current Shop url
     */
    public function getFoxrateShopUrl()
    {
        return $this->getConfig()->getShopUrl();
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
        return $this->getKernel()->get('rci.review_totals')->getReviewTotalData($entityId);
    }

    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }

    /**
     * Lazy loader for review model
     */
    public function getReviewModel()
    {
        if (null == $this->reviewModel)
        {
            $this->reviewModel = $this->getKernel()->get('rci.review');
        }
        return $this->reviewModel;
    }

    private function getConfig()
    {
        return $this->getKernel()->get('shop.configuration');
    }
}