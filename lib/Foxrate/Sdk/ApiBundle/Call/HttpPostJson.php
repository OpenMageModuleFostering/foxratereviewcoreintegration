<?php

/**
 * cURL based API call with request data send as POST parameters
 * Response is parsed as JSON
 *
 */
class Foxrate_Sdk_ApiBundle_Call_HttpPostJson
    extends Foxrate_Sdk_ApiBundle_Call_CurlCall
    implements Foxrate_Sdk_ApiBundle_Call_ApiCallInterface
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
        $this->responseObject = json_decode($this->responseData,$this->asAssociativeArray);
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
