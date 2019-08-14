<?php
class Foxrate_ReviewCoreIntegration_Helper_Rating extends Mage_Core_Helper_Abstract
{
    public function getRatingStars($productId)
    {

        $productPage = $this->getKernel()->get('rci.review')->getReviewTotalDataById($productId);
        return '<div class="rating" style="width:' . Mage::helper('reviewcoreintegration')->formatCalcPercent($productPage['average'], 5) . '%"></div>';
    }

    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }
}