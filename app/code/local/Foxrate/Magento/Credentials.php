<?php


class Foxrate_Magento_Credentials implements Foxrate_Sdk_Api_Components_ShopCredentialsInterface
{

    public $authenticator;

    public $config;

    function __construct($config)
    {
        $this->config = $config;
    }

    /**
     *
     */
    public function  saveUserCredentials()
    {
        //skip this. Magento will save.
    }

    /**
     * Get posted username
     * @return mixed
     */
    public function postUsername()
    {
        return $this->getFieldsetDataValue('fox_api_username');
    }

    /**
     * Get posted password
     * @return mixed
     */
    public function postPasword()
    {
        return $this->getFieldsetDataValue('fox_api_password');
    }

    /**
     * Some Api Calls requires Foxrate Shop Id. Save it.
     *
     * @param $shopId
     * @return mixed|void
     */
    public function saveShopId($shopId)
    {
        $this->config->saveShopConfVar('foxrateShopId', 'shop_' . $shopId);
    }


}