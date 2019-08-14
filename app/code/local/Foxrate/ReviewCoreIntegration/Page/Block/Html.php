<?php
class Foxrate_ReviewCoreIntegration_Page_Block_Html extends Mage_Page_Block_Html
{
    
    public function _toHtml()
    {
        if ((bool) $this->getRequest()->getParam('ajax')) {

            $productId = Mage::app()->getRequest()->getParam('product');
            $filterHelper = $this->getKernel()->get('rci.filter_helper');

            $this->assign('filterHelper', $filterHelper);
            $this->assign('productId', $productId);
            $this->assign('processedReviewContainer', $filterHelper->processProductReviews($productId));
        }

        return parent::_toHtml();
    }

    /**
     * @return Foxrate_Kernel
     */
    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }
}