<?php
class Foxrate_Magento_Product implements Foxrate_Sdk_FoxrateRCI_ProductInterface
{
    /**
     * Load product Ids
     *
     * @return mixed
     */
    public function getProductsIds()
    {
        $model = Mage::getModel('catalog/product'); ;
        $collection = $model->getCollection(); ;
        foreach ($collection as $product)
        {
            $productIds[$product->getId()] = $product->getId();
        }
        array_unique($productIds);
        return $productIds;
    }
}