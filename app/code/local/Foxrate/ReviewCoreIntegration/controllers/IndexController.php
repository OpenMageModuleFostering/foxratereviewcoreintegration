<?php

class Foxrate_ReviewCoreIntegration_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        if((bool) $this->getRequest()->getParam('ajax')){ // ?ajax=true
            $this->getLayout()->getBlock('root')->setTemplate('foxrate/review/product/view/foxrate_ajax_list.phtml');  //changes the root template
        }

        $this->renderLayout();
    }

}