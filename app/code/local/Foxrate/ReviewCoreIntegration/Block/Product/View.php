<?php

/**
 * Viewer for foxrate_general_details.phtml template
 *
 * Class Foxrate_ReviewCoreIntegration_Block_Product_View
 *
 */
class Foxrate_ReviewCoreIntegration_Block_Product_View extends Mage_Review_Block_Product_View
{

    protected $prodRevPage;

    protected $prodRevGeneral;

    protected $lazyLoadingModel;

    private static $kernel;

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $productid = $this->getFoxrateProductId();

        $kernel = new Foxrate_Kernel('dev', false);
        $kernel->boot();

        $this->assign('foxrateReviewGeneralData', $kernel->get('rci.review_totals')->getReviewTotalData($productid));
        $this->assign('foxrateProductReviewList', $kernel->get('rci.process_reviews')->getProductReviewList($productid) );
        return parent::_toHtml();
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
        $productid = $this->getFoxrateProductId();
        return  $this->getKernel()->get('rci.review')->getWriteReviewLink($productid);

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

    public function getConfig()
    {
        return $this->lazyLoadModel('reviewcoreintegration/config');
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
        return $this->getKernel()->get('rci.review')->getSortingCriteria();
    }

    /**
     * Make a lazy loader for Magento modules
     *
     * @param $value
     * @return mixed
     */
    public function lazyLoadModel($value)
    {
        if (!isset($this->lazyLoadingModel[$value]))
        {
            $this->lazyLoadingModel[$value] = Mage::getModel($value);
        }

        return $this->lazyLoadingModel[$value];
    }

    public function getKernel()
    {
        if (self::$kernel !== null)
        {
            return self::$kernel;
        }

        $kernel = new Foxrate_Kernel('dev', false);
        $kernel->boot();

        return self::$kernel = $kernel;
    }

    /**
     * Extracts date from specific format
     * @param $date
     * @return mixed
     */
    public function calcReviewDate($date)
    {
        return $this->getKernel()->get('rci.review')->calcReviewDate($date);
    }

}