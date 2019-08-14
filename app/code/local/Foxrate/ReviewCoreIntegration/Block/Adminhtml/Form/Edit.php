<?php
class Foxrate_ReviewCoreIntegration_Block_Adminhtml_Form_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'form';
        $this->_controller = 'adminhtml_form';

        $this->_updateButton('save', 'label', Mage::helper('form')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('form')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), -100);


        $this->_addButton('button1', array(
                'label'     => Mage::helper('adminhtml')->__('Button1'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/button1Click') . '\')',
                'class'     => 'back',
            ),-1,5);

        $this->_addButton('button2', array(
                'label'     => Mage::helper('adminhtml')->__('Button2'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/button2Click') . '\')',
                'class'     => 'save',
            ),-1,3);

        $this->_addButton('button3', array(
                'label'     => Mage::helper('adminhtml')->__('Button3'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/button3Click') . '\')',
                'class'     => 'delete',
            ),-1,1);

        $this->_addButton('button4', array(
                'label'     => Mage::helper('adminhtml')->__('Button4'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/button4Click') . '\')',
                'class'     => 'delete',
            ),-1,4,'footer');

    }

    public function getHeaderText()
    {
        return Mage::helper('form')->__('My Form Container');
    }
}
