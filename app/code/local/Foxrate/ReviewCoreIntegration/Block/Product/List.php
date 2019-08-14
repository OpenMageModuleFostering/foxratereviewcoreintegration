<?php
class Foxrate_ReviewCoreIntegration_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    public function reviewTotalsModel()
    {
        if (null == $this->reviewTotalsModel)
        {
            $this->reviewTotalsModel = $this->getKernel()->get('rci.review_totals');
        }

        return $this->reviewTotalsModel;
    }

    /**
     * @return Foxrate_Kernel
     */
    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }

}