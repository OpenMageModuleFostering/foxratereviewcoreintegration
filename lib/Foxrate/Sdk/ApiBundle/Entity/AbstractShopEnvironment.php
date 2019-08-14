<?php
abstract class Foxrate_Sdk_ApiBundle_Entity_AbstractShopEnvironment
{
    protected $bridgeUrl;

    public function __construct()
    {
        $this->setBridgeUrl(
            $this->bridgeUrl()
        );
    }

    /**
     * @param mixed $bridgeUrl
     */
    public function setBridgeUrl($bridgeUrl)
    {
        $this->bridgeUrl = $bridgeUrl;
    }

    /**
     * @return mixed
     */
    public function getBridgeUrl()
    {
        return $this->bridgeUrl;
    }
}