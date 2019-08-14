<?php
class Foxrate_Sdk_Filter_Channel_ShopSystem implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    private $fieldName = Foxrate_Sdk_Constants_FieldName_Channel::SHOP_SYSTEM;

    public function __construct()
    {
        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();
    }

    public function not($value)
    {
        $expressionBuilder = Foxrate_Sdk_Specification_Criteria::expr();

        $ratingExpression = $expressionBuilder->neq($this->fieldName, $value);
        $this->filterCriteria->where($ratingExpression);
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->andWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }
}