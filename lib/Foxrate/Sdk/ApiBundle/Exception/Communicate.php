<?php

class Foxrate_Sdk_ApiBundle_Exception_Communicate extends Foxrate_Sdk_ApiBundle_Exception_ModuleException
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