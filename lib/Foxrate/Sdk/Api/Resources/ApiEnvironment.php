<?php


class Foxrate_Sdk_Api_Resources_ApiEnvironment implements  Foxrate_Sdk_Api_Resources_ApiEnvironmentInterface
{

    protected $environment;

    const DEV_API_URI_ = 'http://foxrate.vm/',
            PROD_API_URI_ = 'http://fb.foxrate.de/';

    /**
     * Class constructor
     * @param  string $environment
     */
    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    public function getFoxrateApiUri()
    {
        switch($this->environment)
        {
            case ('dev'):
                //legacy support
                return self::DEV_API_URI_;
                break;
            case ('prod'):
                //legacy support
                return self::PROD_API_URI_;
                break;
            default:
                throw new RuntimeException('Current environment is not supported.');
        }

        //$uri = constant(strtoupper($this->environment).'_API_URI');
    }
}
