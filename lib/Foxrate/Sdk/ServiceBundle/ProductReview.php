<?php

class Foxrate_Sdk_ServiceBundle_ProductReview
{
    private $productReviewList;

    public function __construct(Foxrate_Sdk_ListBundle_ProductReview $productReviewList)
    {
        $this->productReviewList = $productReviewList;
    }

    public function getCount($userId, $productIDList)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getCount()
        );
    }

    public function getOverallRating($userId, $productIDList)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getRatingAverage()
        );
    }

    public function getQualityRating($userId, $productIDList)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $filterBuilder->setQualityRatingNot(0);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getQualityRatingAverage()
        );
    }

    public function getQualityRatingCount($userId, $productIDList)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $filterBuilder->setQualityRatingNot(0);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getQualityRatingCount()
        );
    }

    public function getPriceRating($userId, $productIDList)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $filterBuilder->setPriceRatingNot(0);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getPriceRatingAverage()
        );
    }

    public function getPriceRatingCount($userId, $productIDList)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $filterBuilder->setPriceRatingNot(0);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getPriceRatingCount()
        );
    }

    public function getCountByRating($userId, $productIDList, $rating)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $filterBuilder->setRating($rating);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getCount()
        );
    }

    public function getRecommendCount($userId, $productIDList)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $filterBuilder->setRecommend(true);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getRecommendCount()
        );
    }

    public function getTotalRecommendVoteCount($userId, $productIDList)
    {
        $productReviewList = $this->getNewProductList();
        $filterBuilder = $this->getFilterBuilderWithStandartSetup($productIDList);

        $filterBuilder->setRecommendNot(false);

        $productReviewList->addFilters($filterBuilder->getFilters());
        return $productReviewList->getAggregated(
            $userId,
            Foxrate_Sdk_Specification_AggregateFactory_ProductReview::getRecommendCount()
        );
    }

    private function getNewProductList()
    {
        $this->productReviewList->clear();
        return $this->productReviewList;
    }

    private function getFilterBuilderWithStandartSetup($productIDList)
    {
        $filterBuilder = new Foxrate_Sdk_Builder_Filter_ProductReview();
        $filterBuilder->setProductIDList($productIDList);
        $filterBuilder->setSuspicious(false);
        $filterBuilder->setDisplay(true);
        return $filterBuilder;
    }
}