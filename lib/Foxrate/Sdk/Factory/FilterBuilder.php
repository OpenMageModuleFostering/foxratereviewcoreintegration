<?php


class Foxrate_Sdk_Factory_FilterBuilder
{

    private $minDate;

    private $maxDate;

    public function __construct($minDate, $maxDate)
    {
        $this->maxDate = $maxDate;
        $this->minDate = $minDate;
    }


    public function get($name)
    {
        $method = 'getFilterBuilder' . $name;

        if (!is_callable(array($this, $method))) {
            throw new Foxrate_Sdk_ApiBundle_Exception_ModuleException(
                sprintf(
                    'Method %s not found.',
                    $method
                )
            );
        }

        return $this->$method();
    }

    private function getFilterBuilderWithStandartSetup()
    {
        $filterBuilder = new Foxrate_Sdk_Builder_Filter_SellerRating();
        $filterBuilder->setDateLimit($this->minDate, $this->maxDate);
        $filterBuilder->setSuspicious(false);
        $filterBuilder->setDisplay(true);
        return $filterBuilder;
    }
}
 