<?php

class Foxrate_Sdk_ApiBundle_Caller_ApiCaller implements Foxrate_Sdk_ApiBundle_Caller_ApiCallerInterface
{
    private $options;
    private $lastCall;
    private $engine;
    private $freshConnect = true;

    /**
     * @param array                  $options Options array
     *
     * @throws Exception When the cURL library can't be found
     */
    public function __construct($options)
    {
        $this->options = $options;
        $this->engine = null;
    }

    public function call(Foxrate_Sdk_ApiBundle_Call_ApiCallInterface $call)
    {
        if ($call instanceof Foxrate_Sdk_ApiBundle_Call_CurlCall) {
                $this->engine = new Foxrate_Sdk_ApiBundle_Helper_Curl();
        }

        $this->lastCall = $call;
        $result = $call->execute($this->options, $this->engine, $this->freshConnect);

        if ($call instanceof Foxrate_Sdk_ApiBundle_Call_CurlCall) {
            $this->engine->close();
        }

        return $result;
    }
    /**
     * Method returns last status
     *
     * @return string Last status
     */
    public function getLastStatus()
    {
        return $this->lastCall->getStatus();
    }


}
 