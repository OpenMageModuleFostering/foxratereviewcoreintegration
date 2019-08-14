<?php

class Foxrate_ReviewCoreIntegration_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $reviewModel;

    protected $productPage;

    public function detailedRatingHtml()
    {
        $entityId = Mage::app()->getRequest()->getParam('id');
        $this->reviewTotals = $this->getReviewModel()->getReviewTotalDataById($entityId);

        //check empty reviews
        $reviewsCount = $this->getReviewModel()->getTotalReviews($this->reviewTotals);

        if ($reviewsCount == 0)
        {
            $this->setTemplate('rating/empty.phtml');
            return parent::_toHtml();
        }

        $this->assign('reviewLink', $this->getWriteReviewLink($entityId));
        $this->assign('reviewTotals', $this->reviewTotals);
        $this->assign('foxrateReview', $this->getReviewModel());
        $this->assign('entityId', $entityId);
        return parent::_toHtml();
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
        $percent = $this->getKernel()->get('rci.review_totals')->calcPercent($current, $total);
        return number_format($percent, 2, ".", "");
    }

    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }

    /**
     * Lazy loader for review model
     */
    public function getReviewModel()
    {
        return $this->getKernel()->get('rci.review');
    }
}