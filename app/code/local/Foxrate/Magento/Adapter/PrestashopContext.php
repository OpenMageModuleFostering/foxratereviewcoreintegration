<?php

/**
 * This is Magento specific class , to adapt Magento $this->context
 * Class Foxrate_Magento_Adapter_MagentoContext
 */
class Foxrate_Magento_Adapter_MagentoContext {

    protected $context;

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

}
