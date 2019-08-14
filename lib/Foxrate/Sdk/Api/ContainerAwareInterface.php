<?php
/**
 * ContainerAwareInterface should be implemented by classes that depends on a Container.
 *
 * @api
 */
interface Foxrate_Sdk_Api_ContainerAwareInterface
{
    /**
     * Sets the Container.
     *
     * @param Foxrate_Sdk_Api_ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(Foxrate_Sdk_Api_ContainerInterface $container = null);
}