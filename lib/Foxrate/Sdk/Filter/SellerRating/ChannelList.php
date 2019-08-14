<?php
class Foxrate_Sdk_Filter_SellerRating_ChannelList implements Foxrate_Sdk_Interface_Filter
{
    private $filterCriteria;

    public function __construct($channelList)
    {
        $this->filterCriteria = Foxrate_Sdk_Specification_Criteria::create();

        foreach ($channelList as $channel) {
            $filter = new Foxrate_Sdk_Filter_SellerRating_Channel(
                $channel->type,
                $channel->id
            );
            $this->filterCriteria = $filter->applyFilter($this->filterCriteria);
        }
    }

    public function applyFilter(Foxrate_Sdk_Specification_Criteria $criteria)
    {
        $criteria->andWhere($this->filterCriteria->getWhereExpression());
        return $criteria;
    }
}