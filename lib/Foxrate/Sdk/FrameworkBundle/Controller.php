<?php

/**
 * Base Controller class
 *
 * Class Foxrate_Sdk_FrameworkBundle_Controller
 */
class Foxrate_Sdk_FrameworkBundle_Controller extends Foxrate_Sdk_FrameworkBundle_DependencyInjection_ContainerAware
{

    /**
     * Access service container
     * @param $id
     *
     * @return object
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

}
