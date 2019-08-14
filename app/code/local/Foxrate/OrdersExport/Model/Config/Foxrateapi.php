<?php

class Foxrate_OrdersExport_Model_Config_Foxrateapi extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        if($this->getField() == 'fox_api_username') {
            $user = $this->getFieldsetDataValue('fox_api_username');
            $pass = $this->getFieldsetDataValue('fox_api_password');

            // check if user exists
            $foxrate = Mage::getModel('ordersexport/foxrate');
            $userExists = $foxrate->isUserExist($user, $pass);
            if(!$userExists) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('User does not exist.'));
            } else {
                Mage::getSingleton('adminhtml/session')->setFoxrateRunSetShopUrl(1);
            }
        }
    }

    public function afterLoad()
    {
        $run = Mage::getSingleton('adminhtml/session')->getFoxrateRunSetShopUrl();

        if(!empty($run)) {
            Mage::getSingleton('adminhtml/session')->setFoxrateRunSetShopUrl(null);

            $user = Mage::getStoreConfig('ordersexport/foxrateOrdersExport/fox_api_username');
            $pass = Mage::getStoreConfig('ordersexport/foxrateOrdersExport/fox_api_password');

            $foxrate = Mage::getModel('ordersexport/foxrate');

            // create channels in foxrate
            try {
                $result = $foxrate->setShopModuleUrl($user, $pass);
                $this->saveShopId($result->shop_id);
            } catch (Exception $e) {

                $msg = Mage::helper('adminhtml')->__('Some errors occured: <br>');
                $msg .= $e->getMessage() ."<br>";
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__($msg));
            }

        }
    }

    public function saveShopId($shopId)
    {
        Mage::getModel('reviewcoreintegration/config')->saveShopConfVar('string', 'foxrateShopId', 'shop_' . $shopId);
    }

}