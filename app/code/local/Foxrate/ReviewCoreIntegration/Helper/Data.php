<?php

class Foxrate_ReviewCoreIntegration_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $reviewModel;

    protected $productPage;

    public function detailedRatingHtml()
    {
        $entityId = Mage::app()->getRequest()->getParam('id');
        $foxReviewModel = Mage::getModel('reviewcoreintegration/review');
        $this->reviewTotals = $foxReviewModel->getReviewTotalDataById($entityId);

        //check empty reviews
        $reviewsCount = $foxReviewModel->getTotalReviews($this->reviewTotals);

        if ($reviewsCount == 0)
        {
            $this->setTemplate('rating/empty.phtml');
            return parent::_toHtml();
        }

        $this->assign('reviewLink', $this->getWriteReviewLink($entityId));
        $this->assign('reviewTotals', $this->reviewTotals);
        $this->assign('foxrateReview', $foxReviewModel);
        $this->assign('entityId', $entityId);
        return parent::_toHtml();
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

    /**
     * Extracts date from specific format
     * @param $date
     * @return mixed
     */
    public function calcReviewDate($date)
    {
        return $this->getReviewModel()->calcReviewDate($date);
    }

    /**
     * Calculates percent
     * @param $current
     * @param $total
     * @return mixed
     */
    public function formatCalcPercent($current, $total)
    {
        $percent = Mage::getModel('reviewcoreintegration/reviewtotals')->calcPercent($current, $total);
        return number_format($percent, 2, ".", "");
    }

}