<?php

/**
 * Foxrate FI review summary helper
 * Class Foxrate_ReviewCoreIntegration_Block_Review_Form
 */
class Foxrate_ReviewCoreIntegration_Block_Review_Form extends Mage_Review_Block_Form
{

    protected $reviewModel;

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        try {
            $productId = $this->getFoxrateProductId();
            $viewHelper = $this->getKernel()->get('rci.process_reviews');
            $reviewPage = $viewHelper->getRawProductReviews($productId);

            $this->assign('productId', $productId);
            $this->assign('foxrateReviewGeneralData', $this->getKernel()->get('rci.review_totals')->getReviewTotalData($productId));
            $this->assign('viewHelper', $viewHelper);
            $this->assign('reviewHelper',  $this->getKernel()->get('rci.review_helper'));
            $this->assign('reviewPage', $reviewPage);
            $this->assign('pages', $this->getReviewModel()->getPageNav($reviewPage->pages_count, $reviewPage->current_page));

            return parent::_toHtml();

        } catch (Foxrate_Sdk_ApiBundle_Exception_ModuleException $e) {
            return parent::_toHtml();
        }

    }

    /**
     * Replace review summary html with more detailed review summary
     * Reviews collection count will be jerked here
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false, $displayIfNoReviews = false)
    {
        try {
            $prodRevGeneral =  $this->getProdRevGeneral();

            return
                $this->getLayout()->createBlock('rating/entity_detailed')
                    ->setEntityId($this->getProduct()->getId())
                    ->toHtml()
                .
                $this->getLayout()->getBlock('product_review_list.count')
                    ->assign('count', $prodRevGeneral['count'])
                    ->toHtml()
                ;
        } catch (Foxrate_Sdk_ApiBundle_Exception_Communicate $e) {
            return parent::getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
        }
    }

    /**
     * Return currently shown product id
     * @return mixed
     */
    public function getFoxrateProductId()
    {
        return $this->getEntityId();
    }

    /**
     * Get ajax controller url
     * @return mixed
     */
    public function getAjaxControllerUrl(){

        return $this->getConfig()->getAjaxControllerUrl();

    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        if (!isset($this->entityId))
        {
            $this->entityId = Mage::app()->getRequest()->getParam('id');
        }

        return $this->entityId;
    }

    /**
     * Returns page numbers ready for navigating
     * @return array
     */
    public function getPageNav()
    {
        return $this->getKernel()->get('rci.process_reviews')->getPageNav();
    }

    /**
     * Deactivates standart oxid reviews, reliable more than changing db record
     * Foxrate reviews are used instead
     */
    public function isReviewActive()
    {
        return false;
    }

    /**
     * Deactivates standart oxid review star display
     * @return bool
     */
    public function ratingIsActive()
    {
        return false;
    }

    /**
     * Gets link to write user review
     * @return null
     *
     */
    public function getWriteReviewLink()
    {
        $productId = $this->getFoxrateProductId();
        return  $this->getReviewModel()->getWriteReviewLink($productId);

    }

    /**
     * Controller return true or false if richsnippet options is enabled or disabled
     * @return bool
     */
    public function richSnippetIsActive()
    {

        $config = $this->getConfig();
        $isActive = $config->getConfigParam('foxratePR_OrderRichSnippet');
        if($isActive=='off' || is_null($isActive))
        {
            return false;
        }
        else
        {
            return true;
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
     * Create review sorting criteria
     * @return array
     */
    public function getSortingCriteria()
    {
        return $this->getReviewModel()->getSortingCriteria();
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

    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }

    /**
     * Get shop configuration
     * @return mixed
     */
    private function getConfig()
    {
        return $this->getKernel()->get('shop.configuration');
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

}
