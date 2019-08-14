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

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        try {
            $productid = $this->getFoxrateProductId();

            $this->assign('foxrateReviewGeneralData', $this->getKernel()->get('rci.review_totals')->getReviewTotalData($productid));
            $this->assign('foxrateProductReviewList', $this->getKernel()->get('rci.process_reviews')->getProductReviewList($productid) );
            return parent::_toHtml();

        } catch (Exception $e) {
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
     * Extracts date from specific format
     * @param $date
     * @return mixed
     */
    public function calcReviewDate($date)
    {
        return $this->getKernel()->get('rci.review')->calcReviewDate($date);
    }

    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }

    private function getConfig()
    {
        return $this->getKernel()->get('shop.configuration');
    }
}