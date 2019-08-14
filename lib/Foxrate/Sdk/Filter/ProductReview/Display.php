<?php
class Foxrate_Sdk_Filter_ProductReview_Display implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    private $fieldName = Foxrate_Sdk_Constants_FieldName_ProductReview::DISPLAY;

    public function __construct($isDisplayed)
    {
        $expressionBuilder = Foxrate_Sdk_Specification_Criteria::expr();

        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();
        $this->filterCriteria->where($expressionBuilder->eq($this->fieldName, $isDisplayed));
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->andWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }
}