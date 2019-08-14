<?php


class Foxrate_Sdk_DirectorBundle_Filter_SellerRating
{
    private $minDate;

    private $maxDate;

    private $dataSource;

    public function __construct($minDate, $maxDate, Foxrate_Sdk_Interface_DataSource $dataSource)
    {
        $this->maxDate = $maxDate;
        $this->minDate = $minDate;
        $this->dataSource = $dataSource;
    }

    public function getStandart()
    {
        $filterBuilder = new Foxrate_Sdk_Builder_Filter_SellerRating();
        $filterBuilder->setSuspicious(false);
        $filterBuilder->setDisplay(true);
        return $filterBuilder;
    }

    public function getDateLimited()
    {
        $filterBuilder = new Foxrate_Sdk_Builder_Filter_SellerRating();
        $filterBuilder->setDateLimit($this->minDate, $this->maxDate);
        return $filterBuilder;
    }

    public function getByUser($userId)
    {
        $filterBuilder = $this->getStandart();
        if ($this->dataSource->isGranted($userId, 'ROLE_SELLER_RATING_DATE_LIMIT')) {
            $filterBuilder->mergeFilters($this->getDateLimited()->getFilters());
        }
        return $filterBuilder;
    }
}