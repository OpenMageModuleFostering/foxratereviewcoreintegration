<?php

/**
 * @todo What is type is this class? Helper? Controller?
 *
 * Class Foxrate_Sdk_FoxrateRci_Processreviews
 */
class Foxrate_Sdk_FoxrateRci_Processreviews
{

    protected $reviewModel;
    protected $dataManager;
    protected $processedReviews;
    protected $foxrateGeneralData;

    function __construct(Foxrate_Sdk_FoxrateRci_DataManager $dataManager, Foxrate_Sdk_FoxrateRCI_ReviewInterface $reviewModel, $request)
    {
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
        try
        {
            $objData = $this->dataManager->loadCachedProductReviews($productId);
            $pageRevInfo = $this->getReviewModel()->convertObjectToArray($objData);
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

    /**
     * Returns page numbers ready for navigating
     * @return array
     */
    public function getPageNav()
    {
        $this->processedReviews = $this->getProcessedReviews();

        if (!isset($this->processedReviews['pages_count']) || !isset($this->processedReviews['current_page']))
        {
            return '';
        }
        return $this->getReviewModel()->getPageNav($this->processedReviews['pages_count'], $this->processedReviews['current_page']);;
    }

    public function getProductReviewList($productId)
    {
        if (null == $this->processedReviews)
        {
            $this->processedReviews = $this->getRawProductReviews($productId);
        }

        return isset($this->processedReviews['reviews']) ? $this->processedReviews['reviews'] : array();
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
            throw new Exeption ("Reviews needs to be processed or retrieved at first.");
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

    //this is not recommended!
    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }


}