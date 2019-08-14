<?php

class Foxrate_Sdk_ServiceBundle_Channel
{
    private $channelList;

    private $cumulativeChannelService;

    public function __construct(
        Foxrate_Sdk_ListBundle_Channel $channelList,
        Foxrate_Sdk_ServiceBundle_CumulativeChannel $cumulativeChannelService
    ) {
        $this->channelList = $channelList;
        $this->cumulativeChannelService = $cumulativeChannelService;
    }

    public function getActiveChannelArray($userId)
    {
        if ($this->getActiveChannelCount($userId) == 0) {
            throw new Foxrate_Sdk_Exception_ServiceException('Active channels not found');
        }
        $channelList = $this->getNewChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_Channel();
        $filterBuilder->setState('on');

        $channelList->addFilters($filterBuilder->getFilters());
        return $channelList->getArray($userId);
    }

    public function getActiveChannelCount($userId)
    {
        $channelList = $this->getNewChannelList();

        $filterBuilder = new Foxrate_Sdk_Builder_Filter_Channel();
        $filterBuilder->setState('on');

        $channelList->addFilters($filterBuilder->getFilters());
        $channelCount = $channelList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_Channel::getChannelCount()
        );
        $cumulativeChannelCount = $this->cumulativeChannelService->getActiveChannelCount($userId);
        return $channelCount + $cumulativeChannelCount;
    }
    

    public function getNewChannelList()
    {
        $this->channelList->clear();
        return $this->channelList;
    }

}