<?php


class Foxrate_Sdk_Strategy_AbstractOverall
{
    protected $filterDirector;

    protected $channelService;

    protected $cumulativeChannelService;

    protected $sellerRatingList;

    protected $overallByChannel;

    public function __construct(
        Foxrate_Sdk_ServiceBundle_CumulativeChannel $cumulativeChannelService,
        Foxrate_Sdk_ServiceBundle_Channel $channelService,
        Foxrate_Sdk_DirectorBundle_Filter_SellerRating $filterDirector,
        Foxrate_Sdk_ListBundle_SellerRating $sellerRatingList
    ) {
        $this->cumulativeChannelService = $cumulativeChannelService;
        $this->channelService = $channelService;
        $this->filterDirector = $filterDirector;
        $this->sellerRatingList = $sellerRatingList;
    }


    /**
     * @duplicate
     * @return mixed
     */
    protected function getNewSellerRatingList()
    {
        $this->sellerRatingList->clear();
        return $this->sellerRatingList;
    }
}
 