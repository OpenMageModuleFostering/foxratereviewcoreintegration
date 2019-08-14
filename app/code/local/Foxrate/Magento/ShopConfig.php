<?php


class Foxrate_Magento_ShopConfig implements Foxrate_Sdk_Api_Components_SavedCredentialsInterface
{

    public function savedUsername()
    {
        return Mage::getStoreConfig('ordersexport/foxrateOrdersExport/fox_api_username');
    }

    public function  savedPassword()
    {
        return Mage::getStoreConfig('ordersexport/foxrateOrdersExport/fox_api_password');
    }

} 