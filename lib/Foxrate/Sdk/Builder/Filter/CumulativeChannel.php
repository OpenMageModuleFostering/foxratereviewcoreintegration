<?php

class Foxrate_Sdk_Builder_Filter_CumulativeChannel{

    private $filters;

    public function setState($state)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_CumulativeChannel_State($state);
    }

    public function setChannelId($channelId)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_CumulativeChannel_Id($channelId);
    }

    public function getFilters()
    {
        return $this->filters;
    }


}