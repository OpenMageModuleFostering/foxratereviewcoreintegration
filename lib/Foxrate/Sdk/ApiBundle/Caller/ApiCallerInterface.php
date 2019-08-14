<?php

/**
 * Interface for API Caller
 */
interface Foxrate_Sdk_ApiBundle_Caller_ApiCallerInterface
{
    /**
     * Execute an API call using a certain method
     *
     * @param Foxrate_Sdk_ApiBundle_Call_ApiCallInterface $call
     * @return string The parsed response of the API call
     */
    public function call(Foxrate_Sdk_ApiBundle_Call_ApiCallInterface $call);

    /**
     * Method returns last status
     *
     * @return string Last status
     */
    public function getLastStatus();

}
