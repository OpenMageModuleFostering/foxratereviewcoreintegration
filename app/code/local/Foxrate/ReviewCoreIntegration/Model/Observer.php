<?php

class Foxrate_ReviewCoreIntegration_Model_Observer
{
    public function importProductReviews()
    {
        if (Mage::getIsDeveloperMode())
        {
            error_reporting(E_ERROR);
        }

        ini_set('memory_limit','1024M');
        set_time_limit(0);

        $foxProdRevs= Mage::getModel('reviewcoreintegration/review');
        $status = $foxProdRevs->importProductReviews();
        echo $status."\n";
    }
}