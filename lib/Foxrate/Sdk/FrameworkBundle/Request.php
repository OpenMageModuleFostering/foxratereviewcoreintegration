<?php

class Foxrate_Sdk_FrameworkBundle_Request implements Foxrate_Sdk_FoxrateRCI_RequestInterface
{
    /**
     * Custom parameters
     *
     * @var Foxrate_Sdk_FrameworkBundle_ParameterBag
     *
     * @api
     */
    public $attributes;

    /**
     * Request body parameters ($_POST)
     *
     * @var Foxrate_Sdk_FrameworkBundle_ParameterBag
     *
     * @api
     */
    public $request;

    /**
     * Query string parameters ($_GET)
     *
     * @var Foxrate_Sdk_FrameworkBundle_ParameterBag
     *
     * @api
     */
    public $query;

    /**
     * Server and execution environment parameters ($_SERVER)
     *
     * @var Foxrate_Sdk_FrameworkBundle_ParameterBag
     *
     * @api
     */
    public $server;

    /**
     * Cookies ($_COOKIE)
     *
     * @var Foxrate_Sdk_FrameworkBundle_ParameterBag
     *
     * @api
     */
    public $cookies;

    /**
     * Headers (taken from the $_SERVER)
     *
     * @var Foxrate_Sdk_FrameworkBundle_ParameterBag
     *
     * @api
     */
    public $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * Constructor.
     *
     * @param array  $query      The GET parameters
     * @param array  $request    The POST parameters
     * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array  $cookies    The COOKIE parameters
     * @param array  $files      The FILES parameters
     * @param array  $server     The SERVER parameters
     * @param string $content    The raw body data
     *
     * @api
     */
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array  $query      The GET parameters
     * @param array  $request    The POST parameters
     * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array  $cookies    The COOKIE parameters
     * @param array  $files      The FILES parameters
     * @param array  $server     The SERVER parameters
     * @param string $content    The raw body data
     *
     * @api
     */
    public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->request = new Foxrate_Sdk_FrameworkBundle_ParameterBag($request);
        $this->query = new Foxrate_Sdk_FrameworkBundle_ParameterBag($query);
        $this->attributes = new Foxrate_Sdk_FrameworkBundle_ParameterBag($attributes);
        $this->cookies = new Foxrate_Sdk_FrameworkBundle_ParameterBag($cookies);
        $this->server = new Foxrate_Sdk_FrameworkBundle_ParameterBag($server);
        $this->headers = new Foxrate_Sdk_FrameworkBundle_ParameterBag($this->getHeaders());

        $this->content = $content;
    }

    public static function createFromGlobals()
    {
        $request = new Foxrate_Sdk_FrameworkBundle_Request($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);

        if ($request->headers->has('content_type') && 0 === strpos($request->headers->get('content_type'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('request_method', 'GET')), array('PUT', 'DELETE', 'PATCH'))
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new Foxrate_Sdk_FrameworkBundle_ParameterBag($data);

        } elseif($request->headers->has('content_type') && 0 === strpos($request->headers->get('content_type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request = new Foxrate_Sdk_FrameworkBundle_ParameterBag($data);
        }

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

    public function getContent()
    {
        if (false === $this->content) {
            throw new LogicException('getContent() can only be called once when using the resource return type.');
        }

        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    /**
     * Gets the HTTP headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = array();
        $contentHeaders = array('content_length' => true, 'content_md5' => true, 'content_type' => true);

        $parameters = $this->server->all();

        if (empty($parameters)) {
            return $headers;
        }
        
        foreach ($parameters as $key => $value) {
            if (0 === strpos($key, 'http_')) {
                $headers[substr($key, 5)] = $value;
            }
            // CONTENT_* are not prefixed with HTTP_
            elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $value;
            }
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['php_auth_user'])) {
            $headers['authorization'] = 'Basic '.base64_encode($headers['php_auth_user'].':'.$headers['php_auth_pw']);
        } elseif (isset($headers['php_auth_digest'])) {
            $headers['authorization'] = $headers['php_auth_digest'];
        }

        return $headers;
    }

    public function takeParameter($name) {
        return $this->request->get($name);
    }
}