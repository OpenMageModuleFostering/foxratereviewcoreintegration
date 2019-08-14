<?php


class Foxrate_Sdk_ApiBundle_Caller_FoxrateApiCaller
{
    const JSON_HEADERS = "Content-type: application/json";

    public function __construct(
        Foxrate_Sdk_ApiBundle_Caller_ApiCaller $caller,
        Foxrate_Sdk_ApiBundle_Resources_ApiEnvironment $environment,
        Foxrate_Sdk_ApiBundle_Components_SavedCredentialsInterface $credentials
    )
    {
        $this->caller = $caller;
        $this->environment = $environment;
        $this->credentials = $credentials;
    }

    public function makeCall($uri, $headers = array())
    {
        $options = new stdClass();
        $options->username = $this->credentials->savedUsername();
        $options->password = $this->credentials->savedPassword();

        if (!empty($headers)) {
            $options->headers = $headers;
        }

        return $this->caller->call(
            new Foxrate_Sdk_ApiBundle_Call_HttpFoxrateAuthPostJson(
                $uri,
                $options,
                false
            )
        );

    }
}
 