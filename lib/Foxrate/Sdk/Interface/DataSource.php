<?php

interface Foxrate_Sdk_Interface_DataSource {
    public function getSellerRatingsAggregated(
        $userId,
        \Foxrate_Sdk_Specification_Criteria $criteria,
        \Foxrate_Sdk_Specification_Aggregate $aggregator
    );

    public function getProductReviewsAggregated(
        $userId,
        \Foxrate_Sdk_Specification_Criteria $criteria,
        \Foxrate_Sdk_Specification_Aggregate $aggregator
    );

    public function getCumulativeChannelsAggregated(
        $userId,
        \Foxrate_Sdk_Specification_Criteria $criteria,
        \Foxrate_Sdk_Specification_Aggregate $aggregator
    );

    public function getChannelsAggregated(
        $userId,
        \Foxrate_Sdk_Specification_Criteria $criteria,
        \Foxrate_Sdk_Specification_Aggregate $aggregator
    );

    public function getChannelArray($userId, \Foxrate_Sdk_Specification_Criteria $criteria);

    public function getAccountArray($accountId, \Foxrate_Sdk_Specification_Criteria $criteria);

    public function saveOrder($accountId, $order);

    public function saveSellerRating($accountId, Foxrate_Sdk_Entities_SellerRating $sellerRating);

    public function isGranted($accountId, $role);

    public function removeCumulativeChannel($userId, \Foxrate_Sdk_Specification_Criteria $criteria);
}
