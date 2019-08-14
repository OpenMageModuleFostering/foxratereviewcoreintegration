<?php


class Foxrate_Sdk_Strategy_OverallContext
{
    protected $strategy;

    public function __construct(Foxrate_Sdk_Strategy_OverallInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function execute($userId)
    {
        return $this->strategy->getOverall($userId);
    }
}
 