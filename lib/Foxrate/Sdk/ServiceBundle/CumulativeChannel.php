<?php

class Foxrate_Sdk_ServiceBundle_CumulativeChannel
{
    private $cumulativeChannelList;

    public function __construct(
        Foxrate_Sdk_ListBundle_CumulativeChannel $cumulativeChannelList
    ) {
        $this->cumulativeChannelList = $cumulativeChannelList;
    }

    public function getAllChannelsOverallRating($userId)
    {
        $cumulativeChannelList = $this->getNewCumulativeChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_CumulativeChannel();
        $filterBuilder->setState('on');

        $cumulativeChannelList->addFilters($filterBuilder->getFilters());
        return $cumulativeChannelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_CumulativeChannel::getRatingAverage()
        );
    }

    public function getOverallRatingByCumulativeChannel($userId, $channelId)
    {
        $cumulativeChannelList = $this->getNewCumulativeChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_CumulativeChannel();
        $filterBuilder->setChannelId($channelId);

        $cumulativeChannelList->addFilters($filterBuilder->getFilters());
        return $cumulativeChannelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_CumulativeChannel::getRatingAverage()
        );
    }

    public function getAllChannelsRatingCount($userId)
    {
        $cumulativeChannelList = $this->getNewCumulativeChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_CumulativeChannel();
        $filterBuilder->setState('on');

        $cumulativeChannelList->addFilters($filterBuilder->getFilters());
        return $cumulativeChannelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_CumulativeChannel::getCount()
        );
    }

    public function getActiveChannelCount($userId)
    {
        $cumulativeChannelList = $this->getNewCumulativeChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_CumulativeChannel();
        $filterBuilder->setState('on');

        $cumulativeChannelList->addFilters($filterBuilder->getFilters());
        return $cumulativeChannelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_CumulativeChannel::getChannelCount()
        );
    }

    public function remove($userId, $channelId)
    {
        $cumulativeChannelList = $this->getNewCumulativeChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_AggregatedChannel();
        $filterBuilder->setChannelId($channelId);

        $cumulativeChannelList->addFilters($filterBuilder->getFilters());
        $cumulativeChannelList->remove($userId);
    }

    public function getNewCumulativeChannelList()
    {
        $this->cumulativeChannelList->clear();
        return $this->cumulativeChannelList;
    }

}