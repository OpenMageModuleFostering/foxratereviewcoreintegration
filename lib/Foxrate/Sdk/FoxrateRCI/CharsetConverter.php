<?php

class Foxrate_Sdk_FoxrateRCI_CharsetConverter {

    protected $object;

    protected $charset;

    protected $cache;

    function __construct($object, $charset)
    {
        $this->charset = $charset;
        $this->object = $object;
    }

    public function __get($property)
    {

        if (isset($this->cache[$property])) {
            return $this->cache[$property];
        }

        if (is_int($this->object->$property)) {
            return $this->object->$property;
        }

        $converted = $this->convertCharset( $this->object->$property );
        $this->cache[$property] = $converted;

        return $converted;
    }

    public function __call($name, $arguments){
        return call_user_func_array(array($this->object, $name), $arguments);
    }

    public function __isset($property){
        return isset($this->object->$property);
    }

    public function convertCharset($item) {
        if (is_string($item)) {
            return iconv("UTF-8", $this->getCharset(), $item);
        }
        return new Foxrate_Sdk_FoxrateRCI_CharsetConverter($item, $this->charset);
    }

    /**
     * @return mixed
     */
    public function getCharset()
    {
        return $this->charset;
    }
}
