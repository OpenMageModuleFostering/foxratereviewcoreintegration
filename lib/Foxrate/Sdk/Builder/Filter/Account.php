<?php

class Foxrate_Sdk_Builder_Filter_Account{

    private $filters;

    public function setId($id)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_Account_Id($id);
    }

    public function getFilters()
    {
        return $this->filters;
    }


}