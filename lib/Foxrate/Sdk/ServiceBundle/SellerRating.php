<?php

class Foxrate_Sdk_ServiceBundle_SellerRating
{
    private $sellerRatingList;

    private $channelService;

    private $cumulativeChannelService;

    private $countStrategy;

    private $filterDirector;

    public function __construct(
        Foxrate_Sdk_ListBundle_SellerRating $sellerRatingList,
        Foxrate_Sdk_Interface_DataSource $dataSource,
        Foxrate_Sdk_ServiceBundle_CumulativeChannel $cumulativeChannelService,
        Foxrate_Sdk_ServiceBundle_Channel $channelService,
        Foxrate_Sdk_Strategy_OverallStrategyResolver $countStrategy,
        Foxrate_Sdk_DirectorBundle_Filter_SellerRating $filterDirector
    ) {
        $this->sellerRatingList = $sellerRatingList;
        $this->dataSource = $dataSource;
        $this->cumulativeChannelService = $cumulativeChannelService;
        $this->channelService = $channelService;
        $this->countStrategy = $countStrategy;
        $this->filterDirector = $filterDirector;
    }

    public function getAggregatedResult($userId, $filters, $aggregator)
    {
        $sellerRatingList = $this->getNewSellerRatingList();
        $sellerRatingList->addFilters($filters, $aggregator);
        return $sellerRatingList->getAggregated($userId, $aggregator);
    }

    public function getCountByRatingAndChannel($userId, $rating, $channelType, $channelId)
    {
        $sellerRatingList = $this->getNewSellerRatingList();

        $filterBuilder = $this->filterDirector->getByUser($userId);
        $filterBuilder->setRating($rating);
        $filterBuilder->setChannel($channelType, $channelId);

        $sellerRatingList->addFilters($filterBuilder->getFilters());
        return $sellerRatingList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getCount()
        );
    }

    public function getCount($userId)
    {
        $sellerRatingList = $this->getNewSellerRatingList();

        $filterBuilder = $this->filterDirector->getByUser($userId);
        $filterBuilder->setChannelList($this->channelService->getActiveChannelArray($userId));

        $sellerRatingList->addFilters($filterBuilder->getFilters());
        $sellerRatingCount = $sellerRatingList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getCount()
        );
        $cumulativeChannelRatingCount = $this->cumulativeChannelService->getAllChannelsRatingCount($userId);
        return $sellerRatingCount + $cumulativeChannelRatingCount;
    }

    public function getCountByRating($userId, $rating)
    {
        $sellerRatingList = $this->getNewSellerRatingList();

        $filterBuilder = $this->filterDirector->getByUser($userId);
        $filterBuilder->setChannelList($this->channelService->getActiveChannelArray($userId));
        $filterBuilder->setRating($rating);

        $sellerRatingList->addFilters($filterBuilder->getFilters());
        return $sellerRatingList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getCount()
        );
    }

    public function getCountByChannel($userId, $channelType, $channelId)
    {
        $sellerRatingList = $this->getNewSellerRatingList();

        $filterBuilder = $this->filterDirector->getByUser($userId);
        $filterBuilder->setChannel($channelType, $channelId);

        $sellerRatingList->addFilters($filterBuilder->getFilters());
        return $sellerRatingList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getCount()
        );
    }

    public function getOverall($userId)
    {
        $context  = new Foxrate_Sdk_Strategy_OverallContext($this->countStrategy->getOverallStrategy($userId));
        return $context->execute($userId);
    }

    /**
     * Fasade method for getOverallByChannel.
     * We will decide which method to use manually.
     *
     * @param $userId
     * @param $channelType
     * @param $channelId
     *
     * @return mixed
     */
    public function getOverallByChannel($userId, $channelType, $channelId)
    {
        return $this->countStrategy->getOverallByChannel($userId, $channelType, $channelId);
    }

    public function getRecommendCount($userId, $channelType, $channelId)
    {
        $sellerRatingList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setRecommend(true);
        $filterBuilder->setChannel($channelType, $channelId);

        $sellerRatingList->addFilters($filterBuilder->getFilters());
        return $sellerRatingList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getRecommendCount()
        );
    }

    public function getTotalRecommendVoteCount($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setRecommendNot(false);
        $filterBuilder->setChannel($channelType, $channelId);


        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getRecommendCount()
        );
    }

    public function getOverallInPercent($userId)
    {
        if ($this->getCount($userId) == 0) {
            return 100;
        }
        return round($this->getOverall($userId) * 20, 1);
    }

    public function getProductDetailsRatingCount($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setProductDetailsRatingNot(0);
        $filterBuilder->setChannel($channelType, $channelId);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getProductDetailsRatingCount()
        );
    }

    public function getOrderProcessRatingCount($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setOrderProcessRatingNot(0);
        $filterBuilder->setChannel($channelType, $channelId);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getOrderProcessRatingCount()
        );
    }

    public function getDeliveryRatingCount($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setDeliveryRatingNot(0);
        $filterBuilder->setChannel($channelType, $channelId);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getDeliveryRatingCount()
        );
    }

    public function getCustomerServiceRatingCount($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setCustomerServiceRatingNot(0);
        $filterBuilder->setChannel($channelType, $channelId);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getCustomerServiceRatingCount()
        );
    }

    public function getProductDetailsRating($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setProductDetailsRatingNot(0);
        $filterBuilder->setChannel($channelType, $channelId);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getProductDetailsRatingAverage()
        );
    }

    public function getOrderProcessRating($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setOrderProcessRatingNot(0);
        $filterBuilder->setChannel($channelType, $channelId);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getOrderProcessRatingAverage()
        );
    }

    public function getDeliveryRating($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setDeliveryRatingNot(0);
        $filterBuilder->setChannel($channelType, $channelId);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getDeliveryRatingAverage()
        );
    }

    public function getCustomerServiceRating($userId, $channelType, $channelId)
    {
        $productReviewList = $this->getNewSellerRatingList();
        $filterBuilder = $this->filterDirector->getByUser($userId);

        $filterBuilder->setCustomerServiceRatingNot(0);
        $filterBuilder->setChannel($channelType, $channelId);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_SellerRating::getCustomerServiceRatingAverage()
        );
    }

    private function getNewSellerRatingList()
    {
        $this->sellerRatingList->clear();
        return $this->sellerRatingList;
    }

    public function save($accountId, $sellerRating)
    {
        if (empty($sellerRating->order)) {
            $sellerRating->order = $this->createOrder();
        }

        $this->dataSource->saveOrder($accountId, $sellerRating->order);

        $this->dataSource->saveSellerRating($accountId, $sellerRating);
    }

    protected function createOrder()
    {
        return new \Foxrate_Sdk_Entities_Order();
    }
}
