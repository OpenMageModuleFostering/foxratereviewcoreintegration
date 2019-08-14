<?php

//Currently I've added here as we do not support php5.3
define('FOX_API_COMMUNICATE_NO_SELLER_ID', 10001);
define('FOX_LOCK_REM_FAIL', 10002);
define('FOX_FAIL_LOAD_FILE_DATA', 10003);
define('FOX_PROD_REV_GEN_REV_NOT_FOUND', 10004);
define('FOX_LOAD_CACHED_PROD_REV_NOT_FOUND', 10005);
define('FOX_CALL_PROD_NO_RESULT', 10006);
define('FOX_CHECK_VALID_REVIEW', 10007);
define('FOX_GET_PROD_IDS_NO_DB_PROD', 10008);
define('FOX_SAVE_PROD_REV_CACHE', 10009);
define('FOX_GET_SAVE_PROD_REV_NOT_IMPORTED', 10010);
define('FOX_CONVERT_REV_TO_PROD_IDS_NO_RESULTS', 10011);
define('FOX_COPY_DIR_CONTENT_NOT_EXIST', 10012);
define('FOX_LOAD_SHOP_ID_COULD_NOT_GET_SHOP_ID', 10013);
define('FOX_READ_FILE_NOT_FOUND', 10014);
define('FOX_API_CALL_BUILD_PARAMS_MISSING', 10015);
define('FOX_CHECK_USER_IS_NOT_SET', 10016);
define('FOX_CHECK_PASS_IS_NOT_SET', 10017);
define('FOX_CHECK_USER_NOT_FOUND', 10018);
define('FOX_REQ_SINGLE_PROD_REV', 10020);
define('FOX_LOAD_SHOP_ID_URL_DOES_NOT_MATCH', 10021);
define('FOX_GET_TOTAL_REVIEWS_INFO_NOT_GIVEN', 10022);


class Foxrate_Sdk_FoxrateRCI_Bundle extends Foxrate_Sdk_FrameworkBundle_Bundle
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
        $this->container->set('rci.rich_snippets', array($this, 'getFoxrate_Sdk_FoxrateRCI_RichSnippets'));

    }

    public function getFoxrate_Sdk_FoxrateRCI_Review()
    {
        return new Foxrate_Sdk_FoxrateRCI_Review(
            $this->container->get("shop.configuration"),
            $this->container->get("api.authenticator"),
            $this->container->get("rci.data_manager"),
            $this->container->get("shop.product"),
            $this->container->get("api.environment"),
            $this->container->get("api.client")
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
            $this->container->get("rci.review_totals"),
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
            $this->container->get("shop.configuration"),
            $this->container->get("api.factory.product_reviews")
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

    public function getFoxrate_Sdk_FoxrateRCI_ShopRoutes()
    {
        return new Foxrate_Sdk_FoxrateRCI_ShopRoutes();
    }

    public function getFoxrate_Sdk_FoxrateRCI_RichSnippets()
    {
        $config =  $this->container->get("shop.configuration");

        return new Foxrate_Sdk_FoxrateRCI_RichSnippets(
            $config
        );
    }


}