<?php
class Foxrate_Sdk_Filter_SellerRating_CustomerServiceRating implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    private $fieldName = Foxrate_Sdk_Constants_FieldName_SellerRating::CUSTOMER_SERVICE_RATING;

    public function __construct()
    {
        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();
    }

    public function not($rating)
    {
        $expressionBuilder = Foxrate_Sdk_Specification_Criteria::expr();
        $ratingExpression = $expressionBuilder->neq($this->fieldName, $rating);
        $this->filterCriteria->where($ratingExpression);
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->andWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }
}