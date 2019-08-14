<?php


class Foxrate_Sdk_ApiBundle_Components_Secure
{
    /**
     * Foxrate secret key
     */
    const FOXRATESECRET = 'Foxrate';

    protected $username;
    protected $password;

    public function __construct($username, $password, Foxrate_Sdk_FrameworkBundle_TranslatorInterface $translator)
    {
        $this->username = $username;
        $this->password = $password;
        $this->translator = $translator;
    }

    /**
     * Check if call is secure and we can provide a data
     *
     * @param $hash
     * @throws Foxrate_Sdk_ApiBundle_Exception_Communicate
     */
    public function checkAndSecure($hash)
    {
        if (!$this->checkToken($hash, $this->username, $this->password)) {
            throw new Foxrate_Sdk_ApiBundle_Exception_Communicate($this->translator->trans('ERROR_FOXRATE_CHECK'));
        }
    }

    /**
     * The GET parameter check will have the value of a secret key which MUST
     * be checked against the FOXRATE user data stored by the plugin
     *
     * @param string $hash
     * @param string $user
     * @param string $password
     * @return bool
     */
    private function checkToken($hash, $user, $password)
    {
        $salt = md5(md5($user . $password) . self::FOXRATESECRET);

        if ($hash == $salt) {
            return true;
        }

        return false;
    }

}