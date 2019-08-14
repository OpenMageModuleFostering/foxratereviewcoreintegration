<?php

/**
 * This exception is thrown, then module experiences error from misconfiguration or unsupported version.
 * Class Foxrate_Sdk_ApiBundle_Exception_ModuleException
 */
class Foxrate_Sdk_ApiBundle_Exception_ModuleException extends Exception
{
    /**
     * Default constructor
     *
     * @param string  $message exception message
     * @param integer $code    exception code
     */
    public function __construct($message = "No data", $code = 0)
    {
        parent::__construct($message, $code);
    }
}