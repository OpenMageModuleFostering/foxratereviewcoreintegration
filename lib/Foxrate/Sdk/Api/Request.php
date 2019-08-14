<?php

class Foxrate_Sdk_Api_Request
{
    /**
     * Storing input parameters needed for foxrate
     * @var array
     */
    protected $params = array();


    public static function createFromGlobals()
    {
        $request = new self();
        $request->setParams($_REQUEST);
        return $request;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function getParam($param)
    {
        return $this->params[$param];
    }

    /**
     * @param string $param
     * @param mixed $value
     */
    public function setParam($param, $value)
    {
        $this->params[$param] = $value;
    }

}