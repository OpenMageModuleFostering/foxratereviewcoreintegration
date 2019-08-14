<?php

class Foxrate_Sdk_ListBundle_AggregatedChannel extends Foxrate_Sdk_ListBundle_AbstractList
{
    private $dataSource;

    public function __construct(Foxrate_Sdk_Interface_DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->criteria = Foxrate_Sdk_Specification_Criteria::create();
    }

    public function getAggregated($userId, $aggregate)
    {
        return $this->dataSource->getAggregatedChannelsAggregated($userId, $this->criteria, $aggregate);
    }

}
