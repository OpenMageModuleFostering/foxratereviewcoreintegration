<?php

/**
 * Class Foxrate_Magento_ShopRoutes
 *
 * In many cases this class should be used as is.
 */
class Foxrate_Magento_ShopRoutes implements  Foxrate_Sdk_ApiBundle_Resources_ShopRoutesInterface
{
    const API_CONTROLLER = 'Foxrate_Sdk_ApiBundle_Controllers_Communicate';

    public $routesMap = array(
        'connection_test' => 'connectionTest',
        'check' => 'getOrders'
    );

    public function getRoutes()
    {
        return $this->routesMap;
    }

    public function getController()
    {
        return self::API_CONTROLLER;
    }
}