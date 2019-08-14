<?php

class Foxrate_Sdk_ServiceBundle_AggregatedChannel
{
    private $aggregatedChannelList;

    public function __construct(
        Foxrate_Sdk_ListBundle_AggregatedChannel $aggregatedChannelList
    ) {
        $this->aggregatedChannelList = $aggregatedChannelList;
    }

    public function getAllChannelsOverallRating($userId)
    {
        $aggregatedChannelList = $this->getNewAggregatedChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_AggregatedChannel();
        $filterBuilder->setState('on');

        $aggregatedChannelList->addFilters($filterBuilder->getFilters());
        return $aggregatedChannelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_AggregatedChannel::getRatingAverage()
        );
    }

    public function getOverallRatingByAggregatedChannel($userId, $channelId)
    {
        $aggregatedChannelList = $this->getNewAggregatedChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_AggregatedChannel();
        $filterBuilder->setChannelId($channelId);

        $aggregatedChannelList->addFilters($filterBuilder->getFilters());
        return $aggregatedChannelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_AggregatedChannel::getRatingAverage()
        );
    }

    public function getAllChannelsRatingCount($userId)
    {
        $aggregatedChannelList = $this->getNewAggregatedChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_AggregatedChannel();
        $filterBuilder->setState('on');

        $aggregatedChannelList->addFilters($filterBuilder->getFilters());
        return $aggregatedChannelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_AggregatedChannel::getCount()
        );
    }

    public function getActiveChannelCount($userId)
    {
        $aggregatedChannelList = $this->getNewAggregatedChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_AggregatedChannel();
        $filterBuilder->setState('on');

        $aggregatedChannelList->addFilters($filterBuilder->getFilters());
        return $aggregatedChannelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_AggregatedChannel::getChannelCount()
        );
    }

    public function getNewAggregatedChannelList()
    {
        $this->aggregatedChannelList->clear();
        return $this->aggregatedChannelList;
    }

}