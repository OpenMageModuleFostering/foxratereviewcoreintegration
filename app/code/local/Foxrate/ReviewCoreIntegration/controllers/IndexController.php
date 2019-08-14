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

    public function exportAction()
    {
        $user = Mage::getStoreConfig('reviewcoreintegration/foxrateReviewCoreIntegration/fox_api_username');
        $pass = Mage::getStoreConfig('reviewcoreintegration/foxrateReviewCoreIntegration/fox_api_password');

        // connection_test
        $connectionTest = $this->getRequest()->getParam('connection_test');
        if(isset($connectionTest)){
            $this->_connectionTest($user);
        }

        $foxrate = Mage::getModel('reviewcoreintegration/foxrate');

        // check salt
        $check = $this->getRequest()->getParam('check');
        $checkSaltOK = $foxrate->check($check, $user, $pass);
        if(empty($check) || !$checkSaltOK) {
            $sMessage = $this->__('Bad salt data');
            die($sMessage);
        }

        $days = (int)$this->getRequest()->getParam('days');
        if(empty($days)) {
            $days = 30;
        }

        $response = $foxrate->getOrders($days);

        // output response
        $sJson = json_encode($response);
        $sJson = mb_convert_encoding($sJson, 'UTF-8', 'auto');

        $status = $foxrate->uploadOrders($sJson, $user, $pass);
        echo $status;

        die();
    }

    protected function _connectionTest($user)
    {
        header("Content-type: text/html; charset=utf-8");
        $array['foxrate_auth_login'] = $user;
        echo json_encode($array);
        exit();
    }
}