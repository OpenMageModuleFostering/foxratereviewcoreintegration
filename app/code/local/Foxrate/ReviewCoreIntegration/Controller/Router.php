<?
class Foxrate_ReviewCoreIntegration_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    public function match(Zend_Controller_Request_Http $request)
    {
        $request->setModuleName('reviewcoreintegration')
            ->setControllerName('index')
            ->setActionName('export');

        return true;
    }
}