<?php
class Foxrate_Sdk_ListBundle_AbstractList
{
    protected $criteria;

    public function filterBy(Foxrate_Sdk_Interface_Filter $filter)
    {
        $this->criteria = $filter->applyFilter($this->criteria);
    }

    public function addFilters($filters)
    {
        foreach ($filters as $filter) {
            $this->criteria = $filter->applyFilter($this->criteria);
        }
    }

    public function clear()
    {
        $this->criteria = Foxrate_Sdk_Specification_Criteria::create();
    }
}