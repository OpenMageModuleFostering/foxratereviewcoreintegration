<?php
/**
 * @api
 */
abstract class Foxrate_Sdk_Api_DependencyInjection_ContainerAware implements Foxrate_Sdk_Api_ContainerAwareInterface
{
    /**
     * @var Foxrate_Sdk_Api_ContainerInterface
     *
     * @api
     */
    public $container;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param Foxrate_Sdk_Api_ContainerInterface $container
     *
     * @api
     */
    public function setContainer(Foxrate_Sdk_Api_ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
