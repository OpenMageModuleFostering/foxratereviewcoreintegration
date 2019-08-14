<?php

class Foxrate_OrdersExport_Model_Config_Foxrateapi extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        defined('FOXRATE_DEBUG') or define('FOXRATE_DEBUG', true);

        if($this->getField() == 'fox_api_username') {

            $kernel = new Foxrate_Kernel('dev', false);
            $kernel->boot();

            $authenticator = $kernel->get('api.authenticator');

            try {
                $authenticator->save(
                    $this->getFieldsetDataValue('fox_api_username'), //user
                    $this->getFieldsetDataValue('fox_api_password') //password
                );
            } catch (Foxrate_Sdk_Api_Exception_Setup $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__($e->getMessage()));
                return;
            }
            //Mage::getSingleton('adminhtml/session')->setFoxrateRunSetShopUrl(1);
        }
    }

    public function afterLoad()
    {

    }

}