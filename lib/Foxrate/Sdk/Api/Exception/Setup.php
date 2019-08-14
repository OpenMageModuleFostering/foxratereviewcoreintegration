<?php


class Foxrate_Sdk_Api_Exception_Setup extends Exception
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