<?php

/**
 * Class to encapsulate PHP cUrl (curl_xxx) functions for unit tests
 */
class Foxrate_Sdk_ApiBundle_Helper_Curl
{
    private $handle;

    /**
     * Constructor stores cUrl handle in object
     *
     * @throws \Exception when php curl library is not installed
     */
    public function __construct()
    {
        if (!function_exists('curl_init')) {
            $class = get_class($this);
            throw new Exception("Class '$class' depends on the PHP cURL library that is currently not installed");
        }
        $this->handle = curl_init();
    }

    /**
     * Magic method to execute curl_xxx calls
     *
     * @param string $name      Method name (should be camelized)
     * @param array  $arguments Method arguments
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        $name = $this->tableize($name);
        if (function_exists("curl_$name")) {
            array_unshift($arguments, $this->handle);

            return call_user_func_array("curl_$name", $arguments);
        }
        throw new Exception("Function 'curl_$name' do not exist, see PHP manual.");
    }

    /**
     * Copied from Doctrine.
     * Convert word in to the format for a Doctrine table name. Converts 'ModelName' to 'model_name'
     *
     * @param  string $word  Word to tableize
     * @return string $word  Tableized word
     */
    public static function tableize($word)
    {
        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $word));
    }

}