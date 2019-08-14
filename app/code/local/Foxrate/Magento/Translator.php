<?php


class Foxrate_Magento_Translator implements Foxrate_Sdk_FrameworkBundle_TranslatorInterface{

    public function trans($message){
        $args = array($message);
        return Mage::app()->getTranslator()->translate($args);
    }

} 