<?php

/**
 * Ajax template helper.
 * Class Foxrate_ReviewCoreIntegration_Page_Block_Html
 */
class Foxrate_ReviewCoreIntegration_Page_Block_Html extends Mage_Page_Block_Html
{
    
    public function _toHtml()
    {
        if ((bool)$this->getRequest()->getParam('ajax')) {
            try {
                $productId = $this->getFoxrateProductId();
                $filterHelper = $this->getKernel()->get('rci.filter_helper');
                $reviewPage = $filterHelper->processProductReviews($productId);

                $this->assign('productId', $productId);
                $this->assign('viewHelper', $filterHelper);
                $this->assign('reviewHelper',  $this->getKernel()->get('rci.review_helper'));
                $this->assign('reviewPage', $reviewPage);
                $this->assign('pages', $this->getReviewModel()->getPageNav($reviewPage->pages_count, $reviewPage->current_page));

            } catch (Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException $e) {
                $this->assign('foxrateFiError', $e->getMessage());
                $this->assign('foxrateFiDebugMessage', new Foxrate_Sdk_FoxrateRCI_Error($e->getMessage(), $e->getCode()));
                
            } catch (Foxrate_Sdk_ApiBundle_Exception_Setup $e) {
                $this->getKernel()->get('shop.configuration')->log('Cannot connect to Foxrate API or setup is not finished.');
            }
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

    public function getId(){
        return Mage::app()->getRequest()->getParam('product');
    }

    /**
     * Lazy loader for review model
     */
    public function getReviewModel()
    {
        return $this->getKernel()->get('rci.review');
    }

    public function getFoxrateProductId(){
        return Mage::app()->getRequest()->getParam('product');
    }
}