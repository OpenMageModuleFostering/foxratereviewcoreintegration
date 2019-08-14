<?php

class Foxrate_Sdk_ListBundle_Channel extends Foxrate_Sdk_ListBundle_AbstractList
{
    private $dataSource;

    public function __construct(Foxrate_Sdk_Interface_DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->criteria = Foxrate_Sdk_Specification_Criteria::create();
    }

    public function getArray($userId)
    {
        return $this->dataSource->getChannelArray($userId, $this->criteria);
    }

    public function getAggregated($userId, $aggregate)
    {
        return $this->dataSource->getChannelsAggregated($userId, $this->criteria, $aggregate);
    }

}
