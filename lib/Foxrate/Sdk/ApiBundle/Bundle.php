<?php


class Foxrate_Sdk_ApiBundle_Bundle extends Foxrate_Sdk_FrameworkBundle_BaseBundle
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->container->set('api.sender', array($this, 'getFoxrate_Components_Sender'));
        $this->container->set('api.factory.product_reviews', array($this, 'getFoxrate_Factory_ProductReviews'));
        $this->container->set('api.client', array($this, 'getFoxrate_ApiClient'));
    }

    public function getFoxrate_Components_Sender()
    {
        return new Foxrate_Sdk_ApiBundle_Components_FoxrateSender(
            $this->container->get('shop.credentials'),
            $this->container->get('api.environment'),
            $this->container->get('translator')
        );
    }

    public function getFoxrate_Factory_ProductReviews()
    {
        return new Foxrate_Sdk_ApiBundle_Service_ProductReviewsFactory();
    }

    public function getFoxrate_Translate()
    {
        return new Foxrate_Sdk_ApiBundle_Translator(
            $this->container->get('shop.configuration')
        );
    }

    public function getFoxrate_ApiClient()
    {
        return new Foxrate_Sdk_ApiBundle_Caller_FoxrateApiCaller(
            new Foxrate_Sdk_ApiBundle_Caller_ApiCaller(array()),
            $this->container->get("api.environment"),
            $this->container->get("shop.credentials")
        );
    }
}