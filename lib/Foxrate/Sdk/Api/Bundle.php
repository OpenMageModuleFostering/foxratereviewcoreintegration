<?php


class Foxrate_Sdk_Api_Bundle extends Foxrate_Sdk_Api_BaseBundle
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->container->set('translator', array($this, 'getFoxrate_Sdk_Api_Translator'));
        $this->container->set('api.sender', array($this, 'getFoxrate_Sdk_Api_Components_Sender'));
        $this->container->set('api.authenticator', array($this, 'getFoxrate_Sdk_Api_Controllers_Authenticator'));
        $this->container->set('api.secure', array($this, 'getFoxrate_Sdk_Api_Components_Secure'));
        $this->container->set('api.environment', array($this, 'getFoxrate_Sdk_Api_Resources_ApiEnvironment'));
        $this->container->set('api.router', array($this, 'getFoxrate_Sdk_Api_Router'));
    }

    public function getFoxrate_Sdk_Api_Translator()
    {
        return new Foxrate_Sdk_Api_Translator(
            $this->container->get('shop.environment')
        );
    }

    public function getFoxrate_Sdk_Api_Components_Sender()
    {
        return new Foxrate_Sdk_Api_Components_FoxrateSender(
            $this->container->get('shop.config'),
            $this->container->get('api.environment'),
            $this->container->get('translator')
        );
    }

    public function getFoxrate_Sdk_Api_Controllers_Authenticator()
    {
        $apiSender = $this->container->get('api.sender');
        $shopEnvironment = $this->container->get('shop.environment');
        $translator = $this->container->get('translator');

        /** @var Foxrate_Sdk_Magento_Credentials $credentials */
        $credentials = $this->container->get('shop.credentials');
        return new Foxrate_Sdk_Api_Controllers_Authenticator(
            $apiSender, $shopEnvironment, $translator, $credentials
        );
    }

    public function getFoxrate_Sdk_Api_Resources_ApiEnvironment()
    {
        return new Foxrate_Sdk_Api_Resources_ApiEnvironment(
            $this->container->get('http_kernel')->getEnvironment()
        );
    }

    public function getFoxrate_Sdk_Api_Components_Secure()
    {
        return new Foxrate_Sdk_Api_Components_Secure(
            $this->container->get('shop.config')->savedUsername(),
            $this->container->get('shop.config')->savedPassword(),
            $this->container->get('translator')
        );
    }

    public function getFoxrate_Sdk_Api_Router()
    {
        return new Foxrate_Sdk_Api_Router(
            $this->container->get('shop.routes')
        );
    }

}
