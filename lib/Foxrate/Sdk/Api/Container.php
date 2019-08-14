<?php
class Foxrate_Sdk_Api_Container implements Foxrate_Sdk_Api_ContainerInterface
{
    protected $parameterBag;
    protected $services;

    protected $loading = array();

    /**
     * Constructor.
     *
     * @param Foxrate_Sdk_Api_ParameterBag $parameterBag A ParameterBagInterface instance
     * @param $serviceContainer
     * @api
     */
    public function __construct($serviceContainer, Foxrate_Sdk_Api_ParameterBag $parameterBag = null)
    {
        $this->parameterBag = null === $parameterBag ? new Foxrate_Sdk_Api_ParameterBag() : $parameterBag;

        $this->services  = $serviceContainer;

        $this->set('service_container', $this);
    }

    /**
     * Sets a service.
     *
     * @param $id
     * @param $service
     */
    public function set($id, $service)
    {
        $id = strtolower($id);

        $this->services[$id] = $service;
    }


    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return Boolean true if the service is defined, false otherwise
     *
     * @api
     */
    public function has($id)
    {
        $id = strtolower($id);

        return isset($this->services[$id]) || method_exists($this, 'get'.strtr($id, array('_' => '', '.' => '_')).'Service');
    }

    /**
     * Gets a service.
     *
     * If a service is defined both through a set() method and
     * with a get{$id}Service() method, the former has always precedence.
     *
     * @param string  $id              The service identifier
     * @param integer $invalidBehavior The behavior when the service does not exist
     *
     * @return object The associated service
     *
     * @throws InvalidArgumentException if the service is not defined
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException When the service is not defined
     *
     * @see Reference
     *
     * @api
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        $id = strtolower($id);

        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        if (isset($this->loading[$id])) {
            throw new ServiceCircularReferenceException($id, array_keys($this->loading));
        }

        if (method_exists($this, $method = 'get'.strtr($id, array('_' => '', '.' => '_')).'Service')) {
            $this->loading[$id] = true;

            try {
                $service = $this->$method();
            } catch (\Exception $e) {
                unset($this->loading[$id]);

                if (isset($this->services[$id])) {
                    unset($this->services[$id]);
                }

                throw $e;
            }

            unset($this->loading[$id]);

            return $service;
        }

        if (self::EXCEPTION_ON_INVALID_REFERENCE === $invalidBehavior) {
            throw new Foxrate_Sdk_Api_Exception_ServiceNotFoundException($id);
        }
    }


    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed  The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     *
     * @api
     */
    public function getParameter($name)
    {
        return $this->parameterBag->get($name);
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return Boolean The presence of parameter in container
     *
     * @api
     */
    public function hasParameter($name)
    {
        return $this->parameterBag->has($name);
    }

    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     *
     * @api
     */
    public function setParameter($name, $value)
    {
        $this->parameterBag->set($name, $value);
    }

}
