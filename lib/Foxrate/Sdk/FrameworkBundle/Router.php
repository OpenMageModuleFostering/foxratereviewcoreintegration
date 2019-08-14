<?php
class Foxrate_Sdk_FrameworkBundle_Router
{

    private $routes;

    private $routeConfig;

    public function __construct(Foxrate_Sdk_ApiBundle_Resources_ShopRoutesInterface $routeConfig)
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
            $this->setRoutes($this->routeConfig->getRoutesMap());
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

                if (is_array($routeMethod)) {
                    return array(
                        $routeMethod[0],
                        $routeMethod[1]
                    );
                }
            }
        }

        if (is_array($this->routeConfig->getDefaultRoute())) {
            return $this->routeConfig->getDefaultRoute();
        }

        throw new InvalidArgumentException('Unable to find methods in controller from provided router.');
    }
}
