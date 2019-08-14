<?php
class Foxrate_Sdk_ApiBundle_Service_ProductReviewsFactory
{
    private $productReviewsFactory;

    public function __construct()
    {
        $this->productReviewsFactory = new Foxrate_Sdk_Factory_ProductReview();
    }

    public function fromStdObject($stdObject)
    {
        return $this->productReviewsFactory->fromStdObject($stdObject);
    }
}