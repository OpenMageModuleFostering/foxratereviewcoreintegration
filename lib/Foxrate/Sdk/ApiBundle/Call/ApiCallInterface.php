<?php

/**
 * Interface of cURL based API Call
 */
interface Foxrate_Sdk_ApiBundle_Call_ApiCallInterface
{
    public function __construct($url,$requestObject,$asAssociativeArray=false);

    /**
     * Get the URL of the call
     */
    public function getUrl();

    /**
     * Get the name of the call
     */
    public function getName();

    /**
     * Get the request parameter data as HTTP Query String
     *
     * @see \Lsw\ApiCallerBundle\Call\ApiCall::generateRequestData()
     */
    public function getRequestData();

    /**
     * Get the request parameter data as PHP object
     */
    public function getRequestObject();

    /**
     * Get the request response data as HTTP Query String
     */
    public function getResponseData();

    /**
     * Get the request response data as PHP object
     */
    public function getResponseObject();

    /**
     * Get the HTTP status of the API call
     */
    public function getStatusCode();

    /**
     * Get the HTTP status of the API call
     */
    public function getStatus();

    /**
     * Execute the call
     *
     * @param array  $options      Array of options
     * @param object $engine       Calling engine
     * @param bool   $freshConnect Make a fresh connection every call
     *
     * @return mixed Response
     */
    public function execute($options, $engine, $freshConnect = false);

}
