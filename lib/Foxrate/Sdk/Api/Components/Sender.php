<?php

/**
 * Class Foxrate_Sdk_Api_Components_Sender
 */

class Foxrate_Sdk_Api_Components_Sender
{

    /**
     * Checks required PHP extensions if available
     */
    protected function checkExtensions()
    {
        if (!function_exists('curl_version')) {
            throw new Foxrate_Sdk_Api_Exception_Communicate($this->translator->trans('ERROR_Foxrate_Sdk_EXTENSION_CURL'));
        }
    }

    /**
     * Make a request via CURL with given headers and params to specific URL
     *
     * @param string $sUrl
     * @param array $assocHeaders
     * @param array $aParams
     *
     * @return mixed
     */
    public function makeCurlCall($sUrl, $assocHeaders = array(), $aParams = null)
    {

        $headers = $this->makeHeaders($assocHeaders);
        $ch = curl_init();

        $opts[CURLOPT_CONNECTTIMEOUT] = 10;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_TIMEOUT] = 60;
        $opts[CURLOPT_HTTPHEADER] = $headers;
        $opts[CURLOPT_URL] = $sUrl;
        if (!is_null($aParams)) {
            $opts[CURLOPT_POSTFIELDS] = $aParams;
        }

        if (defined("Foxrate_Sdk_REMOTE_DEBUG") && Foxrate_Sdk_REMOTE_DEBUG == true )
        {
            $opts[CURLOPT_COOKIE] = 'XDEBUG_SESSION=phpstorm';
        }

        curl_setopt_array($ch, $opts);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($result === false || $code != 200) {

            $en = curl_errno($ch);
            $e = curl_error($ch);
            curl_close($ch);
            return 'HTTP Code: ' . $code . ', cURL errno: ' . $en . ', cURL error: ' . $e;
        }
        curl_close($ch);

        return $result;
    }

    /**
     * Make headers index array from assoc array
     *
     * @param $headersArray
     * @return array
     */
    protected function makeHeaders($headersArray)
    {
        if (!is_array($headersArray)) {
            return array();
        }

        $headers = array();

        foreach ($headersArray as $headerKey => $headerValue) {
            $headers[] =  $headerKey . ": " . $headerValue;
        }

        return $headers;
    }

    /**
     * In strict cases , we can check if our array is really assoc
     *
     * @param $arr
     * @return bool
     */
    private function isAssoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
