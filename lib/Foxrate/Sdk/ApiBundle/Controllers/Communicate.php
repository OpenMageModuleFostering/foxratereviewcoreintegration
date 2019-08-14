<?php

class Foxrate_Sdk_ApiBundle_Controllers_Communicate extends Foxrate_Sdk_FrameworkBundle_Controller
{
    public function connectionTest()
    {
        /** @var Foxrate_Sdk_ApiBundle_Components_SavedCredentialsInterface $shopConfig */
        $credentials = $this->container->get('shop.credentials');

        return new Foxrate_Sdk_FrameworkBundle_JsonResponse(
            array(
                'foxrate_auth_login' => $credentials->savedUsername(),
            )
        );
    }

    public function getOrders($days, $check)
    {
        $this->container->get('api.secure')->checkAndSecure($check);

        $orders = $this->container->get('shop.orders')->getOrders($days);

        return new Foxrate_Sdk_FrameworkBundle_JsonResponse(
            $this->container->get('api.sender')->uploadOrders($orders)
        );
    }

}
