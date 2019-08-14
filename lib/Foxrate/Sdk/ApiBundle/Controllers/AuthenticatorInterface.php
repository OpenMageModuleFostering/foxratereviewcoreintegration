<?php

interface Foxrate_Sdk_ApiBundle_Controllers_AuthenticatorInterface
{
    /**
     * Check Foxrate API credentials and save
     *
     * @param $username
     * @param $password
     */
    public function save($username, $password);

    /**
     *  Check if user exists in Foxrate API
     *
     * @param $username
     * @param $password
     * @return bool
     * @throws Foxrate_Sdk_ApiBundle_Exception_Setup
     */
    public function wrapIsUserExist($username, $password);

}