<?php
interface Foxrate_Sdk_ApiBundle_Entity_ShopEnvironmentInterface
{

    public function shopSystem();

    public function shopSystemVersion();

    public function pluginVersion();

    public function bridgeUrl();

    public function getBridgeUrl();

    public function setBridgeUrl($bridgeUrl);

    public function getShopLanguage();

    public function getShopName();

}
