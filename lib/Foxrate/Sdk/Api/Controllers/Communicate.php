<?php

class Foxrate_Sdk_Api_Controllers_Communicate extends Foxrate_Sdk_Api_Controller
{
    public function connectionTest()
    {
        /** @var Foxrate_Sdk_Magento_ShopConfig $shopConfig */
        $shopConfig = $this->container->get('shop.config');

        return new Foxrate_Sdk_Api_JsonResponse(
            array(
                'foxrate_auth_login' => $shopConfig->savedUsername(),
            )
        );
    }

    public function getOrders($days, $check)
    {
        $this->container->get('api.secure')->isSecureCall($check);

        return new Foxrate_Sdk_Api_JsonResponse(
            $this->container->get('shop.orders')->getOrders($days)
        );
    }

}
