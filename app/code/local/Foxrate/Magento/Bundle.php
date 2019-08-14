<?php


class Foxrate_Magento_Bundle extends Foxrate_Sdk_ApiBundle_Bundle
{
    public function boot()
    {
        $this->container->set('shop.credentials', array($this, 'getFoxrate_Magento_Credentials'));
        $this->container->set('shop.environment', array($this, 'getFoxrate_Magento_ShopEnvironment'));
        $this->container->set('shop.order', array($this, 'getFoxrate_Magento_ShopOrder'));
        $this->container->set('shop.orders', array($this, 'getFoxrate_Magento_ShopOrders'));
        $this->container->set('shop.config', array($this, 'getFoxrate_Magento_ShopConfig'));
        $this->container->set('shop.routes', array($this, 'getFoxrate_Magento_ShopRoutes'));
        $this->container->set('shop.assets', array($this, 'getFoxrate_Magento_Assets'));
        $this->container->set('shop.request', array($this, 'getFoxrate_Magento_Adapter_Request'));
        $this->container->set('shop.product', array($this, 'getFoxrate_Magento_Product'));
        $this->container->set('shop.logger', array($this, 'getFoxrate_Magento_Logger'));
        $this->container->set('shop.configuration', array($this, 'getFoxrate_Magento_Adapter_Config'));
        $this->container->set('shop.translate', array($this, 'getFoxrate_Magento_Translate'));

        $this->container->set('translator', array($this, 'getFoxrate_Translator'));
    }

    public function getFoxrate_Magento_Adapter_Config()
    {
        return new Foxrate_Magento_Adapter_Config();
    }

    public function getFoxrate_Magento_Adapter_Request()
    {
        return new Foxrate_Magento_Adapter_Request();
    }

    public function getFoxrate_Magento_ShopEnvironment()
    {
        return new Foxrate_Magento_ShopEnvironment;
    }

    public function getFoxrate_Magento_Credentials()
    {
        return new Foxrate_Magento_Credentials(
            $this->container->get('shop.configuration')
        );
    }

    public function getFoxrate_Magento_ShopOrder()
    {
        return new Foxrate_Magento_ShopOrder;
    }

    public function getFoxrate_Magento_ShopOrders()
    {
        return new Foxrate_Magento_ShopOrders;
    }

    public function getFoxrate_Magento_ShopConfig()
    {
        return new Foxrate_Magento_ShopConfig();
    }

    public function getFoxrate_Magento_ShopRoutes()
    {
        return new Foxrate_Magento_ShopRoutes();
    }

    public function getFoxrate_Magento_Product()
    {
        return new Foxrate_Magento_Product();
    }

    public function getFoxrate_Magento_Assets()
    {
        return new Foxrate_Magento_Assets();
    }

    public function getFoxrate_Magento_Logger()
    {
        return new Foxrate_Magento_Logger();
    }

    public function getFoxrate_Magento_Translate()
    {
        return new Foxrate_Magento_Translate();
    }

    public function getFoxrate_Translator()
    {
        return new Foxrate_Magento_Translator(
            $this->container->get('shop.configuration')
        );
    }

}
