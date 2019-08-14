<?php

class Foxrate_Sdk_Exception_SdkException extends Exception
{
    public function __construct($message = "No message", $code = 0)
    {
        parent::__construct($message, $code);
    }
}