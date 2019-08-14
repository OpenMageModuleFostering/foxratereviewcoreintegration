<?php


class Foxrate_Sdk_Strategy_OverallFromFeedbacks extends Foxrate_Sdk_Strategy_AbstractOverall implements Foxrate_Sdk_Strategy_OverallInterface
{
    public function getOverall($userId)
    {
        $sellerRatingList = $this->getNewSellerRatingList();

        /** @var Foxrate_Sdk_Builder_Filter_SellerRating $filterBuilder */
        $filterBuilder = $this->filterDirector->getByUser($userId);
        $filterBuilder->setChannelList($this->channelService->getActiveChannelArray($userId));

        $sellerRatingList->addFilters($filterBuilder->getFilters());
        $sellerRating = $sellerRatingList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getRatingAverage()
        );
        $aggregateChannelsRating = $this->cumulativeChannelService->getAllChannelsOverallRating($userId);
        if (!empty($aggregateChannelsRating)) {
            return (($aggregateChannelsRating + $sellerRating) / 2);
        }
        return $sellerRating;
    }
}
 