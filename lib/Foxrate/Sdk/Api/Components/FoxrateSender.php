<?php

/**
 * Class Foxrate_Sdk_Api_Components_FoxrateSender
 */
//discuss this kind extends
class Foxrate_Sdk_Api_Components_FoxrateSender extends Foxrate_Sdk_Api_Components_Sender implements Foxrate_Sdk_Api_Components_SenderInterface
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
        Foxrate_Sdk_Api_Components_SavedCredentialsInterface $credentials,
        Foxrate_Sdk_Api_Resources_ApiEnvironmentInterface $apiEnvironment,
        Foxrate_Sdk_Api_TranslatorInterface $translator,
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
            throw new InvalidArgumentException('System error: all required credentials are not set');
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
        return $this->apiEnvironment->getFoxrateApiUri() . 'feedback_api/';
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
}
