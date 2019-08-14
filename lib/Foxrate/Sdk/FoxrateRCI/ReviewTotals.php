<?php

class Foxrate_Sdk_FoxrateRCI_Reviewtotals
{

    /**
     * Product page general data
     * @var array
     */
    protected $reviewTotalsData;

    protected $reviewModel;

    protected $productId;

    public function __construct(Foxrate_Sdk_FoxrateRCI_ReviewInterface $review)
    {
        $this->reviewModel = $review;
    }

    /**
     * @param $current
     * @param $total
     * @return float
     */
    public function calcPercent($current, $total)
    {
        return ($current*100)/$total;
    }

    /**
     * Get Foxrate product pages - general data and reviews.
     *
     * @param  $productId - optional
     * @return mixed
     */
    public function getReviewTotalData($productId)
    {

        if (null === $this->reviewTotalsData)
        {
            $this->setProductId($productId);
            $this->reviewTotalsData = $this->reviewModel->getReviewTotalDataById($productId);
        }
        return $this->reviewTotalsData;
    }


    /**
     * Overide core getReviewsCount . This is important and cannot moved.
     *
     * @return mixed
     */
    public function getReviewsCount()
    {
        return $this->getTotalReviews();
    }

    public function getTotalReviews()
    {

        //tomake - error handling
        if (isset($this->reviewTotalsData['error']))
        {
            return 0;
        }

        if (!isset($this->reviewTotalsData))
        {
            $this->reviewTotalsData = $this->getReviewTotalData(
                $this->getProductId()
            );
        }

        return  $this->reviewTotalsData['count'];
    }

    public function getEntityId()
    {
        return Mage::app()->getRequest()->getParam('id');
    }

    /**
     * Overide core getRatingSummary method
     *
     * @todo hard coded
     * @return bool
     *
     */
    public function getRatingSummary()
    {
        $productPage = $this->getProductPage();
        return $foxrateReviewHelper->formatCalcPercent($productPage['average'], 5);
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        if (null == $this->productId)
        {
            throw new Exception('Product Id is not set!');
        }

        return $this->productId;
    }

    public function getProductPage()
    {
        return $this->getReviewTotalData($this->getProductId());
    }

}
