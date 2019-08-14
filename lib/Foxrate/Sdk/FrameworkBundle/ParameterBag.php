<?php

class Foxrate_Sdk_FrameworkBundle_ParameterBag
{
    protected $parameters;

    /**
     * Constructor.
     *
     * @param array $parameters An array of parameters
     *
     * @api
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = array();
        $this->add($parameters);
    }

    /**
     * Clears all parameters.
     *
     * @api
     */
    public function clear()
    {
        $this->parameters = array();
    }

    /**
     * Adds parameters to the service container parameters.
     *
     * @param array $parameters An array of parameters
     *
     * @api
     */
    public function add(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->parameters[strtolower($key)] = $value;
        }
    }

    /**
     * Gets the service container parameters.
     *
     * @return array An array of parameters
     *
     * @api
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * Gets a service container parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed  The parameter value
     *
     * @throws Foxrate_Sdk_ParameterNotFoundException if the parameter is not defined
     *
     * @api
     */
    public function get($name)
    {
        $name = strtolower($name);

        if (!array_key_exists($name, $this->parameters)) {
            throw new Foxrate_Sdk_FrameworkBundle_Exception_ParameterNotFoundException($name);
        }

        return $this->parameters[$name];
    }

    /**
     * Sets a service container parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     *
     * @api
     */
    public function set($name, $value)
    {
        $this->parameters[strtolower($name)] = $value;
    }

    /**
     * Returns true if a parameter name is defined.
     *
     * @param string $name The parameter name
     *
     * @return Boolean true if the parameter name is defined, false otherwise
     *
     * @api
     */
    public function has($name)
    {
        return array_key_exists(strtolower($name), $this->parameters);
    }

    /**
     * Removes a parameter.
     *
     * @param string $name The parameter name
     *
     * @api
     */
    public function remove($name)
    {
        unset($this->parameters[strtolower($name)]);
    }
}
