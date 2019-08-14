<?php

class Foxrate_Magento_Adapter_Request
{
    public function post($value){
        return Zend_Controller_Request_Http::getPost($value);
    }
} 