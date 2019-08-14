<?php

abstract class Foxrate_Sdk_FrameworkBundle_BaseBundle extends Foxrate_Sdk_FrameworkBundle_DependencyInjection_ContainerAware
{
    protected $name;

    /**
     * Boots the Bundle.
     */
    public function boot()
    {

    }

    /**
     * Shutdowns the Bundle.
     */
    public function shutdown()
    {
    }

    /**
     * Returns the bundle name (the class short name).
     *
     * @return string The Bundle name
     *
     * @api
     */
    final public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $name = get_class($this);
        $pos = strrpos($name, '\\');

        return $this->name = false === $pos ? $name :  substr($name, $pos + 1);
    }
}
