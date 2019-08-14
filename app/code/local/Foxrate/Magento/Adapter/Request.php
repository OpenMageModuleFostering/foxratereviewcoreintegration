<?php

class Foxrate_Magento_Adapter_Request
{
    public function post($value){
        return Mage::app()->getRequest()->getParam($value);
    }
} 