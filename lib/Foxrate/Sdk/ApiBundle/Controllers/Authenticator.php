<?php



class Foxrate_Sdk_ApiBundle_Controllers_Authenticator extends Foxrate_Sdk_ApiBundle_Components_BaseCredentials implements Foxrate_Sdk_ApiBundle_Controllers_AuthenticatorInterface
{

    protected $sender;

    protected $shopEnvironment;

    protected $translator;

    protected $credentials;

    const SHOP_NAME = 'shop_name';
    const SHOP_MODULE_URL = 'shop_module_url';
    const SHOP_SYSTEM = 'shop_system';
    const SHOP_SYSTEM_VERSION = 'shop_system_version';
    const PLUGIN_VERSION = 'plugin_version';

    public function __construct(
        Foxrate_Sdk_ApiBundle_Components_SenderInterface $sender,
        Foxrate_Sdk_ApiBundle_Entity_ShopEnvironmentInterface $shopEnvironment,
        Foxrate_Sdk_FrameworkBundle_TranslatorInterface $translator,
        Foxrate_Sdk_ApiBundle_Components_ShopCredentialsInterface $credentials
    ) {
        $this->sender = $sender;
        $this->shopEnvironment = $shopEnvironment;
        $this->translator = $translator;
        $this->credentials = $credentials;
    }

    public function save($username, $password)
    {
        try {
            //don't catch by ourself, because some systems does not catch any error.
            $this->wrapIsUserExist($username, $password);

            $this->credentials->saveUserCredentials($username, $password);

            // 2. Set shop to Foxrate interface
            $result = $this->wrapSetShopModuleUrl($username, $password);

            if (isset($result->shop_id))
            {
                $this->credentials->saveShopId($result->shop_id);
            }
        } catch (Foxrate_Sdk_ApiBundle_Exception_Communicate $e) {
            throw new Foxrate_Sdk_ApiBundle_Exception_Setup('Connection problem. ' . $e->getMessage());
        }

    }

    public function wrapIsUserExist($username, $password)
    {
        try {
            $response = $this->sender->isUserExist($username, $password);
        } catch (Foxrate_Sdk_ApiBundle_Exception_ModuleException $e) {
            throw new Foxrate_Sdk_ApiBundle_Exception_Setup($e->getMessage());
        }

        if ($response->error != 'false') {
            throw new Foxrate_Sdk_ApiBundle_Exception_Setup($this->translator->trans('BAD_USERNAME_PASSWORD'));
        }

        if ($response->user_exist == 'true') {
            return true;
        }

        throw new Foxrate_Sdk_ApiBundle_Exception_Setup($this->translator->trans('BAD_USERNAME_PASSWORD'));
    }

    private function wrapSetShopModuleUrl()
    {
        $parameters = array(
            self::SHOP_NAME => $this->shopEnvironment->getShopName(),
            self::SHOP_MODULE_URL => $this->shopEnvironment->getBridgeUrl(),
            self::SHOP_SYSTEM => $this->shopEnvironment->shopSystem(),
            self::SHOP_SYSTEM_VERSION => $this->shopEnvironment->shopSystemVersion(),
            self::PLUGIN_VERSION => $this->shopEnvironment->pluginVersion()
        );

        try {
            $result = $this->sender->setShopModuleUrl($parameters);
        } catch (Foxrate_Sdk_ApiBundle_Exception_ModuleException $e) {
            throw new Foxrate_Sdk_ApiBundle_Exception_Setup($e->getMessage());
        }

        if ($result->error == 'true') {
            throw new Foxrate_Sdk_ApiBundle_Exception_Setup($this->translator->trans('ERROR_SET_SHOP_INFO_FIRST_TIME'));
        }

        return $result;
    }
}
