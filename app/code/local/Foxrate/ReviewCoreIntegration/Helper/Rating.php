<?php
class Foxrate_ReviewCoreIntegration_Helper_Rating extends Mage_Core_Helper_Abstract
{
    public function getRatingStars($productId)
    {

        $productPage = Mage::getModel('reviewcoreintegration/review')->getReviewTotalDataById($productId);
        return '<div class="rating" style="width:' . Mage::helper('reviewcoreintegration')->formatCalcPercent($productPage['average'], 5) . '%"></div>';
    }
}