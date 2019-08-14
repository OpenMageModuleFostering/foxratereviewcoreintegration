<?php

class Foxrate_ReviewCoreIntegration_Helper_Processreviews extends Mage_Core_Helper_Abstract
{

    protected $reviewModel;

    protected $processedReviews;

    protected $foxrateGeneralData;


    public function detailedRatingHtml()
    {
        $entityId = Mage::app()->getRequest()->getParam('id');
        $foxReviewModel = Mage::getModel('reviewcoreintegration/review');
        $productPage = $foxReviewModel->getReviewTotalDataById($entityId);

        //check empty reviews
        $reviewsCount = $foxReviewModel->getTotalReviews($productPage);

        if ($reviewsCount == 0)
        {
            $this->setTemplate('rating/empty.phtml');
            return parent::_toHtml();
        }

        $this->assign('reviewLink', $this->getWriteReviewLink($entityId));
        $this->assign('reviewTotals', $productPage);
        $this->assign('foxrateReview', $foxReviewModel);
        $this->assign('entityId', $entityId);
        return parent::_toHtml();
    }



    /**
     * One page of reviews from variety of users
     *
     * @param $productId
     * @return array
     */
    public function getRawProductReviews($productId)
    {
        $foxProdRevs= $this->getReviewModel();
        try
        {
            $objData = $foxProdRevs->loadCachedProductReviews($productId);
            $pageRevInfo = $foxProdRevs->convertObjectToArray($objData);
        }
        catch (Exception $e)
        {
            $pageRevInfo = array( "error" => $e->getMessage());
        }
        $this->processedReviews = $pageRevInfo;

        return $this->processedReviews;
    }

    /**
     * Processed reviews from variety of users
     *
     * @return array
     */
    public function processProductReviews()
    {
        $filter = array();
        $page = Mage::app()->getRequest()->getPost("page");
        $productId = Mage::app()->getRequest()->getPost("product");
        $filter['star_filter'] = Mage::app()->getRequest()->getPost("star_filter");
        $filter['sort'] = Mage::app()->getRequest()->getPost("sort");
        $filter['search'] = Mage::app()->getRequest()->getPost("frsearch");
        $foxProdRevs = $this->getReviewModel();
        try
        {
            $objData = $foxProdRevs->getFilteredProductRevs($productId, $page, $filter);
            $pageRevInfo = $foxProdRevs->convertObjectToArray($objData);
        }
        catch (Exception $e)
        {
            $pageRevInfo = array( "error" => $e->getMessage());
        }
        $this->processedReviews = $pageRevInfo;
        return $this->processedReviews;
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

    public function reviewTotalsModel()
    {
        return Mage::getModel('reviewcoreintegration/reviewtotals');
    }
    /**
     * Get entity id
     *
     * @return mixed
     */
    public function getEntityId()
    {
        return Mage::app()->getRequest()->getParam('id');
    }

    /**
     * Returns page numbers ready for navigating
     * @return array
     */
    public function getPageNav()
    {
        if (!isset($this->processedReviews['pages_count']) || !isset($this->processedReviews['current_page']))
        {
            return '';
        }
        return $this->getReviewModel()->getPageNav($this->processedReviews['pages_count'], $this->processedReviews['current_page']);;
    }

    /**
     * @param mixed $processedReviews
     */
    public function setProcessedReviews($processedReviews)
    {
        $this->processedReviews = $processedReviews;
    }

    /**
     * @return mixed
     * @throws
     */
    public function getProcessedReviews()
    {
        if (null === $this->processedReviews)
        {
            throw Exeption ("Reviews needs to be processed or retrieved at first.");
        }
        
        return $this->processedReviews;
    }

    public function getReviewDataValue($name)
    {
        $data = $this->getProcessedReviews();
        return $data[$name];
    }

    public function getReviewList()
    {
        $data = $this->getProcessedReviews();
        return $data['reviews'];
    }

    public function isEmptyReviewList()
    {
        return count($this->getReviewList()) > 0;
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

    public function isError()
    {
        $processedReviewContainer = $this->getProcessedReviews();
        return isset($processedReviewContainer['error']);
    }

}