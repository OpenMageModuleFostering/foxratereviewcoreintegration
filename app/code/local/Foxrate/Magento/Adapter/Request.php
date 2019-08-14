<?php


class Foxrate_Magento_Adapter_Request implements  Foxrate_Sdk_FoxrateRCI_RequestInterface
{
    public $request;

    function __construct()
    {
        $this->request = self::createFromGlobals();
    }

    public static function createFromGlobals()
    {
        return new Foxrate_Sdk_FrameworkBundle_ParameterBag(Mage::app()->getRequest()->getParams());
    }

    public function takeParameter($name)
    {
        return $this->request->get($name);
    }
}
