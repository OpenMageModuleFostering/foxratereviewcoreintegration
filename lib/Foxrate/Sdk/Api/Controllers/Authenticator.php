<?php


use Symfony\Component\Config\Definition\Exception\Exception;

class Foxrate_Sdk_Api_Controllers_Authenticator extends Foxrate_Sdk_Api_Components_BaseCredentials implements Foxrate_Sdk_Api_Controllers_AuthenticatorInterface
{

    private $sender;

    private $shopEnvironment;

    private $translator;

    private $credentials;

    // parameters for registering shop
    const SHOP_MODULE_URL = 'shop_module_url';
    const SHOP_SYSTEM = 'shop_system';
    const SHOP_SYSTEM_VERSION = 'shop_system_version';
    const PLUGIN_VERSION = 'plugin_version';

    public function __construct(
        Foxrate_Sdk_Api_Components_SenderInterface $sender,
        Foxrate_Sdk_Api_Entity_ShopEnvironmentInterface $shopEnvironment,
        Foxrate_Sdk_Api_TranslatorInterface $translator,
        Foxrate_Sdk_Api_Components_ShopCredentialsInterface $credentials
    ) {
        $this->sender = $sender;
        $this->shopEnvironment = $shopEnvironment;
        $this->translator = $translator;
        $this->credentials = $credentials;
    }

    /**
     * Check Foxrate API credentials and save
     *
     * @param $username
     * @param $password
     */
    public function save($username, $password)
    {
        //don't catch by ourself, because some systems does not catch any error.
        $this->wrapIsUserExist($username, $password);

        $this->credentials->saveUserCredentials();

        // 2. Set shop to Foxrate interface
        $result = $this->wrapSetShopModuleUrl($username, $password);

        if (isset($result->shop_id))
        {
            $this->credentials->saveShopId($result->shop_id);
        }
    }

    /**
     *  Check if user exists in Foxrate API
     *
     * @param $username
     * @param $password
     * @return bool
     * @throws Foxrate_Sdk_Api_Exception_Setup
     */
    private function wrapIsUserExist($username, $password)
    {

        $response = $this->sender->isUserExist($username, $password);

        if ($response->error != 'false') {
            throw new Foxrate_Sdk_Api_Exception_Setup($this->translator->trans('BAD_USERNAME_PASSWORD'));
        }

        if ($response->user_exist == 'true') {
            return true;
        }

        throw new Foxrate_Sdk_Api_Exception_Setup($this->translator->trans('BAD_USERNAME_PASSWORD'));
    }

    /**
     * Inform Foxrate of the Foxrate export script
     *
     * @return bool
     * @throws Foxrate_Sdk_Api_Exception_Setup
     */
    private function wrapSetShopModuleUrl()
    {
        $parameters = array(
            self::SHOP_MODULE_URL => $this->shopEnvironment->getBridgeUrl(),
            self::SHOP_SYSTEM => $this->shopEnvironment->shopSystem(),
            self::SHOP_SYSTEM_VERSION => $this->shopEnvironment->shopSystemVersion(),
            self::PLUGIN_VERSION => $this->shopEnvironment->pluginVersion()
        );

        $result = $this->sender->setShopModuleUrl($parameters);

        if ($result->error == 'true') {
            throw new Foxrate_Sdk_Api_Exception_Setup($this->translator->trans('ERROR_SET_SHOP_INFO_FIRST_TIME'));
        }

        return $result;
    }
}
