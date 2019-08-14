<?php

/**
 * Api Call for Http HttpPost
 *
 */
class Foxrate_Sdk_ApiBundle_Call_HttpPost extends Foxrate_Sdk_ApiBundle_Call_CurlCall implements Foxrate_Sdk_ApiBundle_Call_ApiCallInterface
{
    /**
    * {@inheritdoc}
    */
    public function generateRequestData()
    {
        $this->requestData = http_build_query($this->requestObject);
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponseData()
    {
        $this->responseObject = $this->responseData;
    }

    /**
     * {@inheritdoc}
     */
    public function makeRequest($curl, $options)
    {
        $curl->setopt(CURLOPT_URL, $this->url);
        $curl->setopt(CURLOPT_POST, 1);
        $curl->setopt(CURLOPT_POSTFIELDS, $this->requestData);
        $curl->setoptArray($options);
        $this->responseData = $curl->exec();
    }

}
