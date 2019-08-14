<?php

interface Foxrate_Sdk_ApiBundle_Resources_ApiEnvironmentInterface
{
    public function getFoxrateUrl();

    public function getFoxrateApiUrl();

    /**
     * Checks if current environment is dev
     * @return boolean
     */
    public function isDevEnvironment();
}
