<?php

interface Foxrate_Sdk_FoxrateRCI_ConfigInterface
{
    public function getShopUrl();

    public function getConfigKey($key);

    public function getLanguageAbbr();

    public function getRegistryVar($name);

    public function getHomeUrl();

    public function getShopConfVar($name);

    public function getTranslationFilePath($lang);

    /**
     * @param $name
     * @return null
     */
    public function getConfigParam($name);

    public function getTempFilesPath();

    public function getCachedReviewsPathTemp();

    public function getAjaxControllerUrl();

    public function saveShopConfVar($name, $var, $type = null);

    public function getCachedReviewsPath();

    public function saveRegistryVar($type = null, $name, $var);

    public function getLogsDir();

    public function getConfigKeys();

    public function writeToLog($message);

}