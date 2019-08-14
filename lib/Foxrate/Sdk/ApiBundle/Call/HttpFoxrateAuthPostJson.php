<?php


class Foxrate_Sdk_ApiBundle_Call_HttpFoxrateAuthPostJson
    extends Foxrate_Sdk_ApiBundle_Call_CurlCall
    implements Foxrate_Sdk_ApiBundle_Call_ApiCallInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateRequestData()
    {

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
        $headers = $this->getRequestObject()->headers;

        $curl->setopt(CURLOPT_CONNECTTIMEOUT, 10);
        $curl->setopt(CURLOPT_RETURNTRANSFER, true);
        $curl->setopt(CURLOPT_TIMEOUT, 60);
        if (!empty($headers)) {
            $curl->setopt(CURLOPT_HTTPHEADER, $headers);
        }
        $curl->setopt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $curl->setopt(
            CURLOPT_USERPWD,
            $this->createAuth(
                $this->getRequestObject()->username,
                $this->getRequestObject()->password
            )
        );
        $curl->setopt(CURLOPT_URL, $this->url);
        if (!empty($this->requestData)) {
            $curl->setopt(CURLOPT_POSTFIELDS, $this->requestData);
        }
        $curl->setoptArray($options);
        $this->responseData = $curl->exec();
    }

    private function createAuth($username, $password)
    {
        return trim($username) . ":" . trim($password);
    }
}
 