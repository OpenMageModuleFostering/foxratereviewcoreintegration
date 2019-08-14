<?php
class Foxrate_Sdk_Filter_ProductReview_ProductIdList implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    public function __construct($productIDList)
    {
        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();

        foreach ($productIDList as $productId) {
            $filter = new Foxrate_Sdk_Filter_ProductReview_ProductId(
                $productId->getId(),
                $productId->getChannelType(),
                $productId->getChannelId()
            );
            $this->filterCriteria = $filter->applyFilter($this->filterCriteria);
        }
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->andWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }
}