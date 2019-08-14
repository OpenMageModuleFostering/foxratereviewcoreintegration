<?php


class Foxrate_Sdk_FrameworkBundle_Bundle extends Foxrate_Sdk_FrameworkBundle_BaseBundle
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->container->set('api.authenticator', array($this, 'getFoxrate_Controllers_Authenticator'));
        $this->container->set('api.secure', array($this, 'getFoxrate_Components_Secure'));
        $this->container->set('api.environment', array($this, 'getFoxrate_Sdk_ApiBundle_Resources_ApiEnvironment'));
        $this->container->set('api.router', array($this, 'getFoxrate_Router'));
        $this->container->set('core.request', array($this, 'getFoxrate_Core_Request'), 'prototype');
    }

    public function getFoxrate_Controllers_Authenticator()
    {
        $apiSender = $this->container->get('api.sender');
        $shopEnvironment = $this->container->get('shop.environment');
        $translator = $this->container->get('translator');

        $credentials = $this->container->get('shop.credentials');
        return new Foxrate_Sdk_ApiBundle_Controllers_Authenticator(
            $apiSender, $shopEnvironment, $translator, $credentials
        );
    }

    public function getFoxrate_Sdk_ApiBundle_Resources_ApiEnvironment()
    {
        return new Foxrate_Sdk_ApiBundle_Resources_ApiEnvironment();
    }

    public function getFoxrate_Components_Secure()
    {
        return new Foxrate_Sdk_ApiBundle_Components_Secure(
            $this->container->get('shop.credentials')->savedUsername(),
            $this->container->get('shop.credentials')->savedPassword(),
            $this->container->get('translator')
        );
    }

    public function getFoxrate_Router()
    {
        return new Foxrate_Sdk_FrameworkBundle_Router(
            $this->container->get('rci.routes')
        );
    }

    public function getFoxrate_Core_Request(){
        return  new Foxrate_Sdk_FrameworkBundle_Request();
    }
}
