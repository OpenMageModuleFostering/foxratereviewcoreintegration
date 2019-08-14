<?php

class Foxrate_ReviewCoreIntegration_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if ($this->getRequest()->getParam('fnc') == 'cache_demand') {
            $productId = $this->getRequest()->getParam('product');
            echo $this->getKernel()->get('rci.review')->cacheOnDemandSingleProductReview($productId);
            exit;
        }

        if ((bool)$this->getRequest()->getParam('ajax')) { // ?ajax=true

            $this->loadLayout();

            $this->getLayout()->getBlock('root')->setTemplate(
                'foxrate/review/product/view/foxrate_ajax_list.phtml'
            ); //changes the root template

            $this->renderLayout();
        }

    }

    public function exportAction()
    {
        $kernel = $this->getKernel();

        $request = $kernel->get('core.request');
        $response = $kernel->handle($request::createFromGlobals());
        $response->send();
        exit;
    }

    public function cronAction()
    {
        $foxProdRevs = $this->getKernel()->get('rci.review');
        $status = $foxProdRevs->importProductReviews();
        echo $status . "\n";
        exit;
    }

    /**
     * @return Foxrate_Kernel
     */
    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }
}