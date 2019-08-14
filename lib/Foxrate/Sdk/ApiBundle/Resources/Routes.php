<?php
class Foxrate_Sdk_ApiBundle_Resources_Routes
{
    const API_CONTROLLER = 'Foxrate_Sdk_ApiBundle_Controllers_Communicate';

    public $routesMap = array(
        'connection_test' => 'connectionTest',
        'check' => 'getOrders'
    );
}

