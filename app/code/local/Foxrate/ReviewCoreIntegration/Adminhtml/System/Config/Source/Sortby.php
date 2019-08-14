<?php
class Foxrate_ReviewCoreIntegration_Adminhtml_Model_System_Config_Source_Sortby
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Option one')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Option two')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Option three')),
        );
    }

}