<?php


class Foxrate_Sdk_FoxrateRCI_Error {

    public $error;

    public $message;

    protected $code;

    function __construct($message, $code = 0)
    {
        $this->message = $message;
        $this->error = true;
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getFormatedCode()
    {
        return sprintf(
            "<!-- Foxrate FI error code: %s -->",
            $this->getCode()
        );
    }
}
