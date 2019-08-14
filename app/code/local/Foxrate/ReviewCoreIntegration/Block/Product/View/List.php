<?php


/**
 * Show review summary and review list on review page.
 * Class Foxrate_ReviewCoreIntegration_Block_Product_View_List
 */
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
        try {
            $productId = $this->getFoxrateProductId();
            $processReviews = $this->getKernel()->get('rci.process_reviews');
            $reviews = $processReviews->getRawProductReviews($productId);

            $this->assign('foxrateReview', $this->getKernel()->get('rci.review'));
            $this->assign('foxrateProductReviews', $processReviews->getRawProductReviews($productId));
            $this->assign('pages', $this->getReviewModel()->getPageNav($reviews->pages_count, $reviews->current_page));
            $this->assign('foxrateReviewGeneralData', $this->getKernel()->get('rci.review_totals')->getReviewTotalData($productId));

            $this->setTemplate('foxrate/review/product/view/foxrate_review_page.phtml');
            return parent::_toHtml();

        } catch (Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException $e) {
            $this->assign('foxrateFiError', $e->getMessage());
            $this->assign('foxrateFiDebugMessage', new Foxrate_Sdk_FoxrateRCI_Error($e->getMessage(), $e->getCode()));
            return parent::_toHtml();

        }
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