<?php

class Foxrate_Sdk_FoxrateRCI_Bundle extends Foxrate_Sdk_Api_Bundle
{
    public function boot()
    {

        $this->container->set('rci.review', array($this, 'getFoxrate_Sdk_FoxrateRCI_Review'));
        $this->container->set('rci.review_helper', array($this, 'getFoxrate_Sdk_FoxrateRCI_ReviewHelper'));
        $this->container->set('rci.rating_helper', array($this, 'getFoxrate_Sdk_FoxrateRCI_RatingHelper'));
        $this->container->set('rci.review_totals', array($this, 'getFoxrate_Sdk_FoxrateRCI_ReviewTotals'));
        $this->container->set('rci.orders_export', array($this, 'getFoxrate_Sdk_FoxrateRCI_OrdersExport'));
        $this->container->set('rci.data_manager', array($this, 'getFoxrate_Sdk_FoxrateRCI_DataManager'));
        $this->container->set('rci.process_reviews', array($this, 'getFoxrate_Sdk_FoxrateRCI_ProcessReviews'));
        $this->container->set('rci.filter_helper', array($this, 'getFoxrate_Sdk_FoxrateRCI_FilterHelper'));
    }

    public function getFoxrate_Sdk_FoxrateRCI_Review()
    {
        return new Foxrate_Sdk_FoxrateRCI_Review(
            $this->container->get("shop.configuration"),
            $this->container->get("api.authenticator"),
            $this->container->get("rci.data_manager")
        );
    }

    public function getFoxrate_Sdk_FoxrateRCI_ReviewTotals()
    {
        return new Foxrate_Sdk_FoxrateRCI_ReviewTotals(
            $this->container->get("rci.review")
        );
    }

    public function getFoxrate_Sdk_FoxrateRCI_ReviewHelper()
    {
        return new Foxrate_Sdk_FoxrateRCI_ReviewHelper(
            $this->container->get("rci.review"),
            $this->container->get("shop.configuration")
        );
    }

    public function getFoxrate_Sdk_FoxrateRCI_ProcessReviews()
    {
        return new Foxrate_Sdk_FoxrateRCI_ProcessReviews(
            $this->container->get("rci.data_manager"),
            $this->container->get("rci.review"),
            $this->container->get("shop.request")
        );
    }

    public function getFoxrate_Sdk_FoxrateRCI_FilterHelper()
    {
        return new Foxrate_Sdk_FoxrateRCI_FilterHelper(
            $this->container->get("shop.configuration"),
            $this->container->get("rci.data_manager"),
            $this->container->get("rci.review"),
            $this->container->get("shop.request")
        );
    }

    public function getFoxrate_Sdk_FoxrateRCI_DataManager()
    {
        return new Foxrate_Sdk_FoxrateRCI_DataManager(
            $this->container->get("shop.configuration")
        );
    }

    public function getFoxrate_Sdk_FoxrateRCI_OrdersExport()
    {
        return new Foxrate_Sdk_FoxrateRCI_OrdersExport();
    }

    public function getFoxrate_Sdk_FoxrateRCI_RatingHelper()
    {
        return new Foxrate_Sdk_FoxrateRCI_RatingHelper(
            $this->container->get("rci.review"),
            $this->container->get("rci.review_totals")
        );
    }
}