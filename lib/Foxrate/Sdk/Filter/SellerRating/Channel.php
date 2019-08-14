<?php
class Foxrate_Sdk_Filter_SellerRating_Channel implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    private $channelTypeFieldName = Foxrate_Sdk_Constants_FieldName_SellerRating::CHANNEL_TYPE;

    private $channelIdFieldName = Foxrate_Sdk_Constants_FieldName_SellerRating::CHANNEL_ID;

    public function __construct($channelType, $channelId)
    {
        $expressionBuilder = Foxrate_Sdk_Specification_Criteria::expr();

        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();
        $channelExpression = $expressionBuilder->andX(
            $expressionBuilder->eq($this->channelIdFieldName, $channelId),
            $expressionBuilder->eq($this->channelTypeFieldName, $channelType)
        );

        $this->filterCriteria->where($channelExpression);
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        if ($criteria->getWhereExpression() == null) {
            return $criteria->andWhere($this->filterCriteria->getWhereExpression());
        }

        if ($this->hasChannelFilter($criteria->getWhereExpression())) {
            return $criteria->orWhere($this->filterCriteria->getWhereExpression());
        }
        return $criteria->andWhere($this->filterCriteria->getWhereExpression());
    }

    public function hasChannelFilter(Foxrate_Sdk_Specification_Expression $expression)
    {
        if ($expression instanceof Foxrate_Sdk_Specification_CompositeExpression) {
            foreach ($expression->getExpressionList() as $expressionListValue) {
                if ($this->hasChannelFilter($expressionListValue)) {
                    return true;
                }
            }
            return false;
        }

        if (($expression->getField() == Foxrate_Sdk_Constants_FieldName_SellerRating::CHANNEL_ID) ||
            ($expression->getField() == Foxrate_Sdk_Constants_FieldName_SellerRating::CHANNEL_TYPE)
        ) {
            return true;
        }
            return false;
    }
}