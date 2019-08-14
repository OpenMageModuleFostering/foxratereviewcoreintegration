<?php

/**
 * Use helpers as endpoints for interaction with our libraries (Catch, Log, Show errors)
 *
 * Class Foxrate_Sdk_FoxrateRCI_ReviewHelper
 */
class Foxrate_Sdk_FoxrateRCI_ReviewHelper
{

    protected $reviewModel;

    protected $reviewTotalsModel;

    protected $productPage;

    protected $config;

    function __construct($reviewModel,
                         $reviewTotalsModel,
                         Foxrate_Sdk_FoxrateRCI_ConfigInterface $config
    ) {
        $this->reviewModel = $reviewModel;

        $this->reviewTotalsModel = $reviewTotalsModel;

        $this->config = $config;
    }

    public function getReviewTotalDataById($productId)
    {
        return $this->getReviewModel()->getReviewTotalDataById($productId);
    }

    public function getReviewModel()
    {
        return $this->reviewModel;
    }

    public function getReviewTotalsModel()
    {
        return $this->reviewTotalsModel;
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
        return $this->config->getShopUrl();
    }

    /**
     * Gets Current Shop url
     * @deprecated This method cannot be called in Oxid eshop!
     */
    public function getFoxrateProductId()
    {
        return $this->config->getFoxrateProductId();
    }

    public function getTitle()
    {
        return $this->config->getTitle();
    }

    public function richSnippetIsActive($type = null){
        return $this->reviewModel->richSnippetIsActive();
    }

    public function getSortingCriteria(){
        return $this->reviewModel->getSortingCriteria();
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
     * @param $prodId
     * @return mixed
     */
    public function getWriteReviewLink($prodId)
    {
        return $this->reviewModel->getWriteReviewLink($prodId);
    }



}