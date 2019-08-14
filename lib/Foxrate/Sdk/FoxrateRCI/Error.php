<?php


class Foxrate_Sdk_FoxrateRCI_Error {

    public $error;

    public $message;

    function __construct($message)
    {
        $this->message = $message;
        $this->error = true;
    }
}
