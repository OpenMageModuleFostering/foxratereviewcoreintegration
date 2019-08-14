<?php

/**
 * @todo What is type is this class? Helper? Controller?
 *
 * Class Foxrate_Sdk_FoxrateRCI_ProcessReviews
 */
class Foxrate_Sdk_FoxrateRCI_ProcessReviews
{

    protected $reviewModel;
    protected $dataManager;
    protected $processedReviews;
    protected $foxrateGeneralData;

    public function __construct(
        Foxrate_Sdk_FoxrateRCI_DataManager $dataManager,
        Foxrate_Sdk_FoxrateRCI_Review $reviewModel,
        $request
    ) {
        $this->dataManager = $dataManager;
        $this->reviewModel = $reviewModel;
        $this->request = $request;
    }

    /**
     * One page of reviews from variety of users
     *
     * @param $productId
     * @return array
     */
    public function getRawProductReviews($productId)
    {
        return $this->processedReviews = $this->dataManager->loadCachedProductReviews($productId);
    }

    /**
     * Lazy loader for review model
     */
    public function getReviewModel()
    {
        return $this->reviewModel;
    }

    public function reviewTotalsModel()
    {
        return $this->getKernel()->get('rci.review_totals');
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

    public function getProductReviewList($productId)
    {
        if (null == $this->processedReviews) {
            $this->processedReviews = $this->getRawProductReviews($productId);
        }

        return isset($this->processedReviews->reviews) ? $this->processedReviews->reviews : array();
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
        if (null === $this->processedReviews) {
            //throw new Foxrate_Sdk_ApiBundle_Exception_ModuleException ("Reviews needs to be processed or retrieved at first.");
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
        $reviewPage = $this->getProcessedReviews();
        return isset($reviewPage['error']);
    }

}