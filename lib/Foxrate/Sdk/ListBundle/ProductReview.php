<?php

class Foxrate_Sdk_ListBundle_ProductReview extends Foxrate_Sdk_ListBundle_AbstractList
{
    private $dataSource;

    public function __construct(Foxrate_Sdk_Interface_DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->criteria = Foxrate_Sdk_Specification_Criteria::create();
    }

    public function getAggregated($userId, $aggregate)
    {
        return $this->dataSource->getProductReviewsAggregated($userId, $this->criteria, $aggregate);
    }

    public function clear()
    {
        $this->criteria = Foxrate_Sdk_Specification_Criteria::create();
    }
}
