<?php

class Foxrate_Sdk_Builder_Filter_Channel{

    private $filters;

    public function setState($state)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_Channel_State($state);
    }

    public function getFilters()
    {
        return $this->filters;
    }


}