<?php
/**
 * @api
 */
abstract class Foxrate_Sdk_FrameworkBundle_DependencyInjection_ContainerAware implements Foxrate_Sdk_FrameworkBundle_ContainerAwareInterface
{
    /**
     * @var Foxrate_Sdk_FrameworkBundle_ContainerInterface
     *
     * @api
     */
    public $container;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param Foxrate_Sdk_FrameworkBundle_ContainerInterface $container
     *
     * @api
     */
    public function setContainer(Foxrate_Sdk_FrameworkBundle_ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
