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

    public function updateContentTemplate($event)
    {
        if ($event->getEvent()->getBlock()->getNameInLayout() == 'content') {

            $kernel = new Foxrate_Kernel('dev', false);
            $kernel->boot();
            if ($kernel->get('rci.review')->richSnippetIsActive() )
            {
                $normalOutput = $event->getTransport()->getHtml();

                $normalOutput = str_replace('<div class="product-view">', '<div class="product-view" itemscope itemtype="http://schema.org/Product">', $normalOutput);
                $normalOutput = str_replace('<h1>', '<h1 itemprop="name">', $normalOutput);

                $event->getTransport()->setHtml( $normalOutput );
            }
        }
    }
}