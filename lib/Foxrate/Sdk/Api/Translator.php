<?php


class Foxrate_Sdk_Api_Translator implements Foxrate_Sdk_Api_TranslatorInterface {

    protected $translations;

    //fixme. Make it without hardcode
    public function __construct(Foxrate_Sdk_Api_Entity_ShopEnvironmentInterface $environment)
    {
        $currentLanguage = $environment->getShopLanguage();

        $tranlationsFilePath = dirname(__FILE__) . '/Resources/Translations/' . $currentLanguage . '.php';

        if (!file_exists($tranlationsFilePath)) {
            throw new RuntimeException('Translation file not found: ' . $tranlationsFilePath);
        }

        $this->translations = require ($tranlationsFilePath);
    }

    public function trans($name)
    {
        return $this->translations[$name];
    }
}
