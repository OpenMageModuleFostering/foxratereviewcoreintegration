<?php


class Foxrate_Magento_Credentials implements
    Foxrate_Sdk_ApiBundle_Components_ShopCredentialsInterface,
    Foxrate_Sdk_ApiBundle_Components_SavedCredentialsInterface
{

    public $authenticator;

    public $config;

    function __construct(Foxrate_Sdk_FoxrateRCI_ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     *
     */
    public function  saveUserCredentials()
    {
        $this->config->saveShopConfVar('foxrateUsername', $this->postUsername());
        $this->config->saveShopConfVar('foxratePassword', $this->postPasword());
    }

    /**
     * Get posted username
     * @return mixed
     */
    public function postUsername()
    {
        $groups = Mage::app()->getRequest()->getParam('groups');
        return $groups['foxrateReviewCoreIntegration']['fields']['fox_api_username']['value'];
    }

    /**
     * Get posted password
     * @return mixed
     */
    public function postPasword()
    {
        $groups = Mage::app()->getRequest()->getParam('groups');
        return $groups['foxrateReviewCoreIntegration']['fields']['fox_api_password']['value'];
    }

    /**
     * Some Api Calls requires Foxrate Shop Id. Save it.
     *
     * @param $shopId
     * @return mixed|void
     */
    public function saveShopId($shopId)
    {
        $this->config->saveShopConfVar('foxrateShopId', $shopId);
    }

    public function savedUsername()
    {
        return Mage::getStoreConfig('reviewcoreintegration/foxrateReviewCoreIntegration/fox_api_username');
    }

    public function  savedPassword()
    {
        return Mage::getStoreConfig('reviewcoreintegration/foxrateReviewCoreIntegration/fox_api_password');
    }

}
