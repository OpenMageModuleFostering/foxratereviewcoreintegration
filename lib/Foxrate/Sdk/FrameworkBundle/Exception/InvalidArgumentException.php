<?php

class Foxrate_Sdk_FrameworkBundle_Exception_InvalidArgumentException extends InvalidArgumentException
{
    /**
     * Default constructor
     *
     * @param string  $message exception message
     * @param integer $code    exception code
     */
    public function __construct($message = "No message.", $code = 0)
    {
        parent::__construct($message, $code);
    }
}