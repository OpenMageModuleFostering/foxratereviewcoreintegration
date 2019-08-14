<?php

class Foxrate_Sdk_Builder_Filter_AggregatedChannel{

    private $filters;

    public function setState($state)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_AggregatedChannel_State($state);
    }

    public function setChannelId($channelId)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_AggregatedChannel_Id($channelId);
    }

    public function getFilters()
    {
        return $this->filters;
    }


}