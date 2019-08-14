<?php

class Foxrate_ReviewCoreIntegration_Block_Product_View extends Mage_Review_Block_Product_View
{

    protected $prodRevPage;

    protected $prodRevGeneral;

    protected $lazyLoadingModel;

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
        $oProduct = $this->getProduct();
        $sProductId = $oProduct->oxarticles__oxid->value;
        return $sProductId;
    }

    /**
     * Returns page numbers ready for navigating
     * @return array
     */
    public function getPageNav()
    {
        $foxrate = $this->lazyLoadingModel('reviewcoreintegration/review');
        $pageNav = $foxrate->getPageNav($this->prodRevPage['pages_count'], $this->prodRevPage['current_page']);
        return $pageNav;
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
        $prodId = $this->getFoxrateProductId();
        $foxrate = $this->lazyLoadModel('reviewcoreintegration/review');
        $link = $foxrate->getWriteReviewLink($prodId);
        return $link;

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


}