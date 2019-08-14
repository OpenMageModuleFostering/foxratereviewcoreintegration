<?php

/**
 * Class Foxrate_Sdk_ApiBundle_Components_FoxrateSender
 */
//discuss this kind extends
class Foxrate_Sdk_ApiBundle_Components_FoxrateSender extends Foxrate_Sdk_ApiBundle_Components_Sender implements Foxrate_Sdk_ApiBundle_Components_SenderInterface
{

    protected $headers;

    protected $translator;

    protected $credentials;

    // headers
    const FOXRATE_AUTH_LOGIN = "FoxrateAuthLogin";
    const FOXRATE_AUTH_PASSWORD = "FoxrateAuthPassword";
    const FOXRATE_RESPONSE_TYPE = "FoxrateResponseType";
    const FOXRATE_REQUEST_TYPE = "FoxrateRequestType";

    public function __construct(
        Foxrate_Sdk_ApiBundle_Components_SavedCredentialsInterface $credentials,
        Foxrate_Sdk_ApiBundle_Resources_ApiEnvironmentInterface $apiEnvironment,
        Foxrate_Sdk_FrameworkBundle_TranslatorInterface $translator,
        $logger = null
    ) {
        $this->credentials = $credentials;
        $this->apiEnvironment = $apiEnvironment;
        $this->translator = $translator;

        if (isset($logger))
        {
            $this->logger = $logger;
        }

    }

    /**
     * Remote call to check if credentials are correct shop module in Foxrate interface
     *
     * @param $username
     * @param $password
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function isUserExist($username, $password)
    {

        $this->checkExtensions();

        $this->createHeaders(
            $username,
            $password
        );

        if (!$this->isSetCredentials()) {
            throw new InvalidArgumentException('All required credentials are not set');
        }

        return json_decode(
            $this->makeCurlCall(
                $this->getApiUrl() . 'is_user_exist.php',
                $this->getHeaders()
            )
        );
    }

    /**
     * Remote call to register shop module in Foxrate interface
     *
     * @param $parameters
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function setShopModuleUrl($parameters)
    {
        $this->createHeaders(
            $this->credentials->savedUsername(),
            $this->credentials->savedPassword()
        );

        if (!$this->isSetCredentials()) {
            throw new InvalidArgumentException('System error: all required credentials are not saved.');
        }

        $this->setHeaderValue(self::FOXRATE_REQUEST_TYPE, 'POST');

        return json_decode(
            $this->makeCurlCall(
                $this->getApiUrl() . 'set_shopmodule_url.php',
                $this->getHeaders(),
                $parameters
            )
        );
    }

    /**
     * Get Foxrate API url
     *
     * @return string
     */
    private function getApiUrl()
    {
        return $this->apiEnvironment->getFoxrateUrl() . 'feedback_api/';
    }

    /**
     * Create headers
     *
     * @param $user
     * @param $password
     * @param string $sDataTypeRequest
     * @param string $sDataTypeResponse
     */
    public function createHeaders($user, $password, $sDataTypeRequest = 'JSON', $sDataTypeResponse = 'JSON')
    {
        $this->setHeaders(
            array(
                self::FOXRATE_AUTH_LOGIN => $user,
                self::FOXRATE_AUTH_PASSWORD => $password,
                self::FOXRATE_RESPONSE_TYPE => $sDataTypeResponse,
                self::FOXRATE_REQUEST_TYPE => $sDataTypeRequest,
            )
        );
    }

    public function uploadOrders($orders)
    {
        return $this->uploadAuthorizedOrders(
            json_encode($orders),
            $this->credentials->savedUsername(),
            $this->credentials->savedPassword()
        );
    }

    /**
     * Uploads order data to remote server
     * @param $json
     * @param $apiUsername
     * @param $apiPassword
     * @return string
     *
     */
    public function uploadAuthorizedOrders($json, $apiUsername, $apiPassword)
    {

        $basicAPILogins = array();
        $basicAPILogins['BasicUser'] = $apiUsername;
        $basicAPILogins['BasicPass'] = $apiPassword;

        try
        {
            $uploadData= $this->queryFoxrateApi($basicAPILogins, "/v1/uploadUrl/generate.json?type=orders", "");
            $this->uploadDataHttpPut($uploadData->upload_url, $json);
            $this->queryFoxrateApi($basicAPILogins, "/v1/uploadUrl/{$uploadData->upload_id}.json", "");
        }
        catch(Foxrate_Sdk_ApiBundle_Exception_ModuleException $ex)
        {
            $status = array(
                "error" => 'true',
                "error_msg" => "{$ex->getMessage()}"
            );
            return $status;
        }

        $status = array(
            "error" => 'false',
            "upload_id" => $uploadData->upload_id
        );
        return $status;
    }

    /**
     * Queries the Foxrate API
     * @param $basicAPILogins
     * @param $apiUrlPartial
     * @param $postFields
     * @return mixed
     * @throws Foxrate_Sdk_ApiBundle_Exception_ModuleException
     */
    public function queryFoxrateApi($basicAPILogins, $apiUrlPartial, $postFields = NULL){
        $apiUrl = $this->getFoxrateAPIUrl() . $apiUrlPartial;
        $handle = curl_init();
        $opts = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $basicAPILogins['BasicUser'] .":". $basicAPILogins['BasicPass'],
            CURLOPT_URL => $apiUrl,
        );
        curl_setopt_array($handle, $opts);
        if(isset($postFields)){
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $postFields);
        }

        $apiResponseRaw = curl_exec($handle);
        $curlError = curl_error($handle);
        $ApiResponse = json_decode($apiResponseRaw);

        if(!empty($curlError)){
            throw new Foxrate_Sdk_ApiBundle_Exception_ModuleException($curlError);
        }

        if(isset($ApiResponse->status) && $ApiResponse->status == 'error'){
            throw new Foxrate_Sdk_ApiBundle_Exception_ModuleException($apiResponseRaw);
        }

        return $ApiResponse;
    }

    /**
     * Uploads the string to specified url using CURL's HTTP PUT method
     * @param $uploadUrl
     * @param $sData
     * @throws Foxrate_Sdk_ApiBundle_Exception_ModuleException
     */
    private function uploadDataHttpPut($uploadUrl, $sData){
        $temp = tmpfile();
        fwrite($temp, $sData);
        rewind($temp);
        $handle = curl_init();

        $options = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_URL => $uploadUrl,
            CURLOPT_PUT => TRUE,
            CURLOPT_INFILE => $temp,
            CURLOPT_INFILESIZE => strlen($sData),
            CURLOPT_BINARYTRANSFER => TRUE,
            CURLOPT_RETURNTRANSFER => true
        );

        curl_setopt_array($handle, $options);
        $serverError = curl_exec($handle);
        $curlError = curl_error($handle);
        fclose($temp);

        if(!empty($curlError)){
            throw new Foxrate_Sdk_ApiBundle_Exception_Setup($curlError);
        }

        if(!empty($serverError)){
            throw new Foxrate_Sdk_ApiBundle_Exception_Communicate($serverError);
        }
    }

    public function getFoxrateAPIUrl()
    {
        return $this->apiEnvironment->getFoxrateApiUrl();
    }


    /**
     * Check , if all required credentials are set
     * @return bool
     */
    protected function isSetCredentials()
    {
        if (null == $this->getHeaderValue(self::FOXRATE_AUTH_LOGIN) || null == $this->getHeaderValue(self::FOXRATE_AUTH_PASSWORD)) {
            return false;
        }
        return true;

    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * @param mixed $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setHeaderValue($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * @param $name
     */
    public function getHeaderValue($name)
    {
        return $this->headers[$name];
    }

    protected function getMockedApiUpload($json)
    {
        return array(
            "error" => 'false',
            "upload_id" => 1,
            "message" => "Developement enviroment enabled, upload orders skipped.",
            "json" => json_decode($json)
        );
    }


}
