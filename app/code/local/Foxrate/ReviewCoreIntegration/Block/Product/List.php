<?php
class Foxrate_ReviewCoreIntegration_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    public function reviewTotalsModel()
    {
        if (null == $this->reviewTotalsModel)
        {
            $this->reviewTotalsModel = Mage::getModel('reviewcoreintegration/reviewtotals');
        }

        return $this->reviewTotalsModel;
    }
}