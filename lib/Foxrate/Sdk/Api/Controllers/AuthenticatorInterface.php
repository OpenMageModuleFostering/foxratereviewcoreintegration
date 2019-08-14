<?php

interface Foxrate_Sdk_Api_Controllers_AuthenticatorInterface
{
    /**
     * Check Foxrate API credentials and save
     *
     * @param $username
     * @param $password
     */
    public function save($username, $password);

}