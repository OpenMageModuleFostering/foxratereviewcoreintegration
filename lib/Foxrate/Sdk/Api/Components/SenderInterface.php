<?php
interface Foxrate_Sdk_Api_Components_SenderInterface
{
    public function makeCurlCall($sUrl, $aHeaders = array(), $aParams = null);

    public function setShopModuleUrl($params);

    public function isUserExist($username, $password);
}
