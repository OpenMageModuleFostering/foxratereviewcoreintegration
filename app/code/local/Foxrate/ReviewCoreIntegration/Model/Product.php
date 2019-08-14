<?php


class Foxrate_ReviewCoreIntegration_Model_Product extends Mage_Catalog_Model_Product
{
    public function getRatingSummary()
    {
        return true;
    }
}