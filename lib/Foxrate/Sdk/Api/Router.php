<?php
class Foxrate_Sdk_Api_Router
{

    private $routes;

    private $routeConfig;

    private $controller;

    public function __construct(Foxrate_Sdk_Api_Resources_ShopRoutesInterface $routeConfig)
    {
        $this->routeConfig = $routeConfig;
        $this->loadRoutes();
    }

    /**
     * @param mixed $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return mixed
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Loads routes and saves and makes them accessible via getters
     */
    public function loadRoutes()
    {
        if (!isset($this->routes)) {
            //load routes!
            $this->setRoutes($this->routeConfig->getRoutes());
            $this->setController($this->routeConfig->getController());
        }
    }

    /**
     * @param $params
     * @return array
     * @throws InvalidArgumentException
     */
    public function createRoute($params)
    {
        $routesMap = $this->getRoutes();

        foreach($routesMap as $routPath => $routeMethod) {
            if (array_key_exists($routPath, $params)) {
                return array(
                    $this->getController(),
                    $routeMethod
                );
            }
        }

        throw new InvalidArgumentException(sprintf('Unable to find router defined methods in %s controller.', $this->getController()));
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

}
