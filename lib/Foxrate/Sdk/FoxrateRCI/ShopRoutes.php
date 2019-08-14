<?php

/**
 * Class Foxrate_Sdk_FoxrateRCI_ShopRoutes
 *
 * In many cases this class should be used as is.
 */
class Foxrate_Sdk_FoxrateRCI_ShopRoutes implements  Foxrate_Sdk_ApiBundle_Resources_ShopRoutesInterface
{
    private $routesMap = array(
        'connection_test' => array('Foxrate_Sdk_ApiBundle_Controllers_Communicate', 'connectionTest'),
        'check' => array('Foxrate_Sdk_ApiBundle_Controllers_Communicate', 'getOrders'),
        'action' => array('Foxrate_Prestashop_Controller_DefaultController', 'indexAction'),
        'show_credentials' => array('Foxrate_Gambio_Controller_CredentialsController', 'showAction'),
        'save_credentials' => array('Foxrate_Gambio_Controller_CredentialsController', 'saveAction'),
        'show_settings' => array('Foxrate_Gambio_Controller_SettingsController', 'showAction'),
        'save_settings' => array('Foxrate_Gambio_Controller_SettingsController', 'saveAction'),
        'cron' => array('Foxrate_Sdk_FoxrateRCI_Controller_CronController', 'indexAction')
    );

    private $defaultRoute = array('Foxrate_Gambio_Controller_CredentialsController', 'indexAction');

    public function getRoutesMap()
    {
        return $this->routesMap;
    }

    /**
     * Get Default route
     * @return array
     */
    public function getDefaultRoute()
    {
        return $this->defaultRoute;
    }
}
