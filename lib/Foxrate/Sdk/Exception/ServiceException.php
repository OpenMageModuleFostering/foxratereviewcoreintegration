<?php

class Foxrate_Sdk_Exception_ServiceException extends Foxrate_Sdk_Exception_SdkException
{
    public function __construct($message = "No message", $code = 0)
    {
        parent::__construct($message, $code);
    }
}