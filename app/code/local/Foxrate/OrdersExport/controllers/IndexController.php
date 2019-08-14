<?php

set_time_limit(0);

/**
 * Shop orders export for Foxrate. This script gets called by Foxrate.
 * Output is in JSON format.
 */

class Foxrate_OrdersExport_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $user = Mage::getStoreConfig('ordersexport/foxrateOrdersExport/fox_api_username');
        $pass = Mage::getStoreConfig('ordersexport/foxrateOrdersExport/fox_api_password');

        // connection_test
        $connectionTest = $this->getRequest()->getParam('connection_test');
        if(isset($connectionTest)){
            $this->_connectionTest($user);
        }

        $foxrate = Mage::getModel('ordersexport/foxrate');

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

    public function editAction()
    {
        exit('hey-ho');
    }
}