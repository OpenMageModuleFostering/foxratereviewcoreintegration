<?php
class Foxrate_Sdk_Filter_SellerRating_Date implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    private $fieldName = Foxrate_Sdk_Constants_FieldName_SellerRating::DATE;

    public function __construct()
    {
        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->andWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }

    public function lessThan($dateString)
    {
        $this->filterCriteria->andWhere(
            Foxrate_Sdk_Specification_Criteria::expr()->lt(
                $this->fieldName,
                strtotime($dateString)
            )
        );
    }

    public function greaterThan($dateString)
    {
        $this->filterCriteria->andWhere(
            Foxrate_Sdk_Specification_Criteria::expr()->gt(
                $this->fieldName,
                strtotime($dateString)
            )
        );
    }
}