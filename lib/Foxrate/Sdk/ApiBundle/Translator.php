<?php

class Foxrate_Sdk_ApiBundle_Translator implements Foxrate_Sdk_FrameworkBundle_TranslatorInterface
{

    protected $config;

    public function __construct(Foxrate_Sdk_FoxrateRCI_ConfigInterface $config)
    {
        $this->config = $config;
        $this->loadTranslation($this->config->getLanguageAbbr());
    }

    public function trans($var, $value = false)
    {
        if ($value)
        {
            return sprintf($var, $value);
        }
        return $this->value($var);
    }

    public function value($var){
        return isset($this->lang[$var]) ? $this->lang[$var] : $var;
    }

    public function htmlEscape($text)
    {
        return $text;
    }

    public function loadTranslation($lang)
    {
        if (!file_exists($this->config->getTranslationFilePath($lang))) {
            $lang = 'en';
        }

        require $this->config->getTranslationFilePath($lang);

        $this->lang = $lang;
    }


} 