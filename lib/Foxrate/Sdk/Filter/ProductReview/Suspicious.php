<?php
class Foxrate_Sdk_Filter_ProductReview_Suspicious implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    private $fieldName = Foxrate_Sdk_Constants_FieldName_ProductReview::SUSPICIOUS;

    public function __construct($isSuspicious)
    {
        $expressionBuilder = Foxrate_Sdk_Specification_Criteria::expr();

        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();
        $this->filterCriteria->where($expressionBuilder->eq($this->fieldName, $isSuspicious));
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->andWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }
}