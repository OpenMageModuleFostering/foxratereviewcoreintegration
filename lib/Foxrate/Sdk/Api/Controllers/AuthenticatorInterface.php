<?php

interface Foxrate_Sdk_Api_Controllers_AuthenticatorInterface
{
    /**
     *  Check if user exists in Foxrate API
     *
     * @param $username
     * @param $password
     * @return bool
     * @throws Foxrate_Sdk_Api_Exception_Setup
     */
    public function isUserExist($username, $password);

    /**
     * Inform Foxrate of the Foxrate export script
     *
     * @return bool
     * @throws Foxrate_Sdk_Api_Exception_Setup
     */
    public function setShopModuleUrl();
}