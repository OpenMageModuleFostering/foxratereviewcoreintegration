<?php
interface Foxrate_Sdk_Api_Entity_ShopEnvironmentInterface
{

    /**
     * Returns the particular shop system version.
     * @return string
     */
    public function shopSystem();

    /**
     * Returns the particular shop system version.
     * @return string
     */
    public function shopSystemVersion();

    /**
     * Returns particular plugin implementation version.
     * @return string
     */
    public function pluginVersion();

    /**
     * Create bridge url - module foxrate api access point
     * @return mixed
     */
    public function bridgeUrl();

    /**
     * Getter
     * @return mixed
     */
    public function getBridgeUrl();

    /**
     * Set bridge Url
     *
     * @param strng $bridgeUrl
     * @return string
     */
    public function setBridgeUrl($bridgeUrl);

    /**
     * Returns shop language
     * @return mixed
     */
    public function getShopLanguage();
}
