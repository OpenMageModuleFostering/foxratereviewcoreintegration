<?php


class Foxrate_Sdk_Strategy_OverallFromChannelAverages
    extends Foxrate_Sdk_Strategy_AbstractOverall
    implements Foxrate_Sdk_Strategy_OverallInterface
{

    public function getOverall($userId)
    {
        $activeChannels = $this->channelService->getActiveChannelArray($userId);
        $activeChannelsRatings = array();
        foreach ($activeChannels as $activeChannel) {
            $rating = $this->getOverallByChannel($userId, $activeChannel->type, $activeChannel->id);
            if (!empty($rating)) {
                $activeChannelsRatings[] = $rating;
            }
        }

        $aggregateChannelsRating = $this->cumulativeChannelService->getAllChannelsOverallRating($userId);
        if (!empty($aggregateChannelsRating)) {
            $activeChannelsRatings[] = $aggregateChannelsRating;
        }
        return (array_sum($activeChannelsRatings)) / count($activeChannelsRatings);
    }

    public function getOverallByChannel($userId, $channelType, $channelId)
    {
        $sellerRatingList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setChannel($channelType, $channelId);

        $sellerRatingList->addFilters($filterBuilder->getFilters());
        return $sellerRatingList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getRatingAverage()
        );
    }

}
