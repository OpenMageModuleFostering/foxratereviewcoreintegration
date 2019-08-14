<?php
class Foxrate_Sdk_Api_ControllerResolver
{
    /**
     * The kernel in which this event was thrown
     * @var
     */
    private $kernel;

    /**
     * The request the kernel is currently processing
     * @var Request
     */
    private $request;

    private $router;

    /**
     * The current controller
     * @var callable
     */
    private $controller;

    public function __construct($kernel, Foxrate_Sdk_Api_Router $router, Foxrate_Sdk_Api_Request $request)
    {
        $this->kernel = $kernel;
        $this->request = $request;
        $this->router = $router;

        $this->setController(
            $this->createController()
        );

    }

    /**
     * @return callable
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sets a new controller
     *
     * @param callable $controller
     *
     * @throws Foxrate_Sdk_Api_Exception_LogicException
     *
     * @api
     */
    public function setController($controller)
    {
        // controller must be a callable
        if (!is_callable($controller)) {
            throw new Foxrate_Sdk_Api_Exception_LogicException(sprintf('The controller must be a callable (%s given).', $this->varToString($controller)));
        }

        $this->controller = $controller;
    }

    private function createController()
    {
        list($class, $method) = $this->router->createRoute(
            $this->request->getParams()
        );

        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $controller = new $class();

        if ($controller instanceof Foxrate_Sdk_Api_ContainerAwareInterface) {
            $controller->setContainer($this->kernel->getContainer());
        }

        return array($controller, $method);
    }

    /**
     *  Returns the kernel in which this event was thrown
     *
     * @return mixed
     *
     * @api
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * Returns the request the kernel is currently processing
     *
     * @return Request
     *
     * @api
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the arguments to pass to the controller.
     *
     * @param Foxrate_Sdk_Api_Request $request
     * @param $controller
     * @return array
     */
    public function getArguments(Foxrate_Sdk_Api_Request $request, $controller)
    {
        if (is_array($controller)) {
            $r = new ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && !$controller instanceof Closure) {
            $r = new ReflectionObject($controller);
            $r = $r->getMethod('__invoke');
        } else {
            $r = new ReflectionFunction($controller);
        }

        return $this->doGetArguments($request, $controller, $r->getParameters());
    }

    protected function doGetArguments(Foxrate_Sdk_Api_Request $request, $controller, array $parameters)
    {
        $attributes = $request->getParams();
        $arguments = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            } else {
                if (is_array($controller)) {
                    $repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                } elseif (is_object($controller)) {
                    $repr = get_class($controller);
                } else {
                    $repr = $controller;
                }

                throw new RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->name));
            }
        }

        return $arguments;
    }

    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }

            return sprintf("Array(%s)", implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string) $var;
    }
}
