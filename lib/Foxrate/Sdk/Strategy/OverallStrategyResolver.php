<?php


class Foxrate_Sdk_Strategy_OverallStrategyResolver
{
    protected $dataSource;

    protected $overallFromFeedback;

    protected $overallFromChannelAverages;

    function __construct(
        Foxrate_Sdk_Interface_DataSource $dataSource,
        Foxrate_Sdk_Strategy_OverallFromFeedbacks $overallFromFeedback,
        Foxrate_Sdk_Strategy_OverallFromChannelAverages $overallFromChannelAverages
    )
    {
        $this->dataSource = $dataSource;
        $this->overallFromChannelAverages = $overallFromChannelAverages;
        $this->overallFromFeedback = $overallFromFeedback;
    }

    /**
     * @param $userId
     * @return Foxrate_Sdk_Strategy_OverallInterface
     */
    public function getOverallStrategy($userId)
    {
        if ($this->dataSource->isGranted($userId, 'ROLE_OVERALL_FROM_CHANNELS')) {
            return $this->overallFromChannelAverages;
        } else {
            return $this->overallFromFeedback;
        }
    }

    /**
     * We decide where to use this method manually.
     *
     * @param $userId
     * @param $channelType
     * @param $channelId
     *
     * @return mixed
     */
    public function getOverallByChannel($userId, $channelType, $channelId)
    {
        return $this->overallFromChannelAverages->getOverallByChannel($userId, $channelType, $channelId);
    }
}
