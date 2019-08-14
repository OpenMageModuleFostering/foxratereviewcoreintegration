<?php
class Foxrate_Sdk_Filter_ProductReview_ProductId implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    private $channelTypeFieldName = Foxrate_Sdk_Constants_FieldName_ProductReview::CHANNEL_TYPE;

    private $channelIdFieldName = Foxrate_Sdk_Constants_FieldName_ProductReview::CHANNEL_ID;

    private $productIdFieldName = Foxrate_Sdk_Constants_FieldName_ProductReview::PRODUCT_ID;

    public function __construct($productId, $channelType, $channelId)
    {
        $expressionBuilder = Foxrate_Sdk_Specification_Criteria::expr();

        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();
        $productExpression = $expressionBuilder->andX(
            $expressionBuilder->eq($this->channelIdFieldName, $channelId),
            $expressionBuilder->eq($this->channelTypeFieldName, $channelType),
            $expressionBuilder->eq($this->productIdFieldName, $productId)
        );

        $this->filterCriteria->where($productExpression);
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->orWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }
}