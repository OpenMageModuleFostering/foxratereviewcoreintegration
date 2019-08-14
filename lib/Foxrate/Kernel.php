<?php
/**
 * Class Foxrate_Kernel
 */
class Foxrate_Kernel extends Foxrate_Sdk_Api_Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new Foxrate_Sdk_Api_Bundle(),
            new Foxrate_Sdk_FoxrateRCI_Bundle(),
            new Foxrate_Magento_Bundle()
        );

        return $bundles;
    }

    /**
     * This contains singleton instance of this class
     * @var null
     */
    private static $instance = null;

    /**
     * implemented singleton for legacy operations
     * @return AppKernel
     */
    public static function getInstance()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        $env = 'prod';

        if (isset($_SERVER['FOXRATE__IS_DEVELOPER_MODE']) && $_SERVER['FOXRATE__IS_DEVELOPER_MODE'] == true) {
            define('DEV', true);
        }

        if (defined('DEV') && DEV) {
            $env = 'dev';
        }
        $debug = false;
        if (defined('DEBUG') && DEBUG) {
            $debug = true;
        }
        $newKernel = new self($env, $debug);
        $newKernel->boot();

        return self::$instance = $newKernel;
    }

    /**
     * Gets a service.
     *
     * it is shortcut for AppKernel::getInstance()->getContainer()->get($id);
     *
     * @param string $id              The service identifier
     * @return object The associated service
     *
     * @see ContainerInterface::get
     *
     */
    public static function get($id)
    {
        //todo currently ->get again boots every bundle and provokes Api/Kernel boot()
        return self::getInstance()->getContainer()->get($id);
    }

    /**
     * Gets a parameter.
     *
     * it is shortcut for AppKernel::getInstance()->getContainer()->getParameter($name);
     *
     * @param string $name The parameter name
     * @return mixed
     *
     * @see ContainerInterface::getParameter
     */
    public static function getParameter($name)
    {
        return self::getInstance()->getContainer()->getParameter($name);
    }
}
