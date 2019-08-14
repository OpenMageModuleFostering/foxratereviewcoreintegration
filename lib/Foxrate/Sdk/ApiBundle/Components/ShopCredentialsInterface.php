<?php

interface Foxrate_Sdk_ApiBundle_Components_ShopCredentialsInterface
{

    /**
     * Check Foxrate API credentials and save
     */
    public function saveUserCredentials();

    /**
     *  Save Foxrate Shop Id. Some Api Calls requires it.
     *
     * @param $shopId
     * @return mixed
     */
    public function saveShopId($shopId);
}
