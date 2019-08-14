<?php

class Foxrate_ReviewCoreIntegration_Page_Block_Html extends Mage_Page_Block_Html
{

    public function _toHtml()
    {
        if ((bool)$this->getRequest()->getParam('ajax')) {
            try {
                $productId = $this->getFoxrateProductId();
                $filterHelper = $this->getKernel()->get('rci.filter_helper');

                $this->assign('productId', $productId);

                $this->assign('filterHelper', $filterHelper);
                $this->assign('reviewHelper', $this->getKernel()->get('rci.review_helper'));

                $this->assign('processedReviewContainer', $filterHelper->processProductReviews($productId));

                $this->assign('pages', $filterHelper->getPageNav());

            } catch (Foxrate_Sdk_ApiBundle_Exception_Setup $e) {

            }
        }

        return parent::_toHtml();
    }

    public function getFoxrateProductId(){
        return Mage::app()->getRequest()->getParam('product');
    }
    
    /**
     * @return Foxrate_Kernel
     */
    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }
}