<?php
/**
 * ContainerAwareInterface should be implemented by classes that depends on a Container.
 *
 * @api
 */
interface Foxrate_Sdk_FrameworkBundle_ContainerAwareInterface
{
    /**
     * Sets the Container.
     *
     * @param Foxrate_Sdk_FrameworkBundle_ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(Foxrate_Sdk_FrameworkBundle_ContainerInterface $container = null);
}