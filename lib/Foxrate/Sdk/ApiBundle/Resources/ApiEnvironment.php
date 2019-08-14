<?php


class Foxrate_Sdk_ApiBundle_Resources_ApiEnvironment implements  Foxrate_Sdk_ApiBundle_Resources_ApiEnvironmentInterface
{

    protected $foxrateApiUrl = 'http://api.foxrate.com';

    protected $foxrateUrl = 'http://foxrate.de/';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->setupDevModeState();
    }

    /**
     * @return mixed
     */
    public function getFoxrateApiUrl()
    {
        return $this->foxrateApiUrl;
    }

    public function getFoxrateUrl()
    {
        return $this->foxrateUrl;
    }

    /**
     * Checks if current environment is dev
     * @return boolean
     */
    public function isDevEnvironment()
    {
        return (!empty($_SERVER['FOXRATE__IS_DEVELOPER_MODE']));
    }

    /**
     * Enables Dev mode, if it is dev environment
     */
    private function setupDevModeState()
    {
        if ($this->isDevEnvironment()) {

            $this->foxrateApiUrl = 'http://api.foxrate.vm';

            $this->foxrateUrl = 'http://foxrate.vm/';

        }
    }
}
