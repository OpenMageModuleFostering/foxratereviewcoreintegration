<?php
class Foxrate_Sdk_Filter_Channel_State implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    private $fieldName = Foxrate_Sdk_Constants_FieldName_Channel::STATE;

    public function __construct($value)
    {
        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();
        $expressionBuilder = Foxrate_Sdk_Specification_Criteria::expr();

        $ratingExpression = $expressionBuilder->eq($this->fieldName, $value);
        $this->filterCriteria->where($ratingExpression);
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->andWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }
}