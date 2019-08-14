<?php

class Foxrate_Sdk_Builder_Filter_SellerRating{

    private $filters;

    public function setSuspicious($value)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_SellerRating_Suspicious($value);
    }

    public function setDisplay($value)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_SellerRating_Display($value);
    }

    public function setChannel($channelType, $channelId)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_SellerRating_Channel($channelType, $channelId);
    }

    public function setChannelList($channelList)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_SellerRating_ChannelList($channelList);
    }


    public function setRecommend($value)
    {
        $recommendFilter = new Foxrate_Sdk_Filter_SellerRating_Recommend($value);
        $recommendFilter->is($value);
        $this->filters[] = $recommendFilter;
    }

    public function setRecommendNot($value)
    {
        $recommendFilter = new Foxrate_Sdk_Filter_SellerRating_Recommend($value);
        $recommendFilter->not($value);
        $this->filters[] = $recommendFilter;
    }

    public function setRating($rating)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_SellerRating_Rating($rating);
    }

    public function setProductDetailsRatingNot($rating)
    {
        $ratingFilter = new Foxrate_Sdk_Filter_SellerRating_ProductDetailsRating();
        $ratingFilter->not($rating);
        $this->filters[] = $ratingFilter;
    }

    public function setOrderProcessRatingNot($rating)
    {
        $ratingFilter = new Foxrate_Sdk_Filter_SellerRating_OrderProcessRating();
        $ratingFilter->not($rating);
        $this->filters[] = $ratingFilter;
    }

    public function setDeliveryRatingNot($rating)
    {
        $ratingFilter = new Foxrate_Sdk_Filter_SellerRating_DeliveryRating();
        $ratingFilter->not($rating);
        $this->filters[] = $ratingFilter;
    }

    public function setCustomerServiceRatingNot($rating)
    {
        $ratingFilter = new Foxrate_Sdk_Filter_SellerRating_CustomerServiceRating();
        $ratingFilter->not($rating);
        $this->filters[] = $ratingFilter;
    }

    public function setDateLimit($minDate, $maxDate)
    {
        $dateFilter = new Foxrate_Sdk_Filter_SellerRating_Date();
        $dateFilter->greaterThan($minDate);
        $dateFilter->lessThan($maxDate);
        $this->filters[] = $dateFilter;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function mergeFilters($filters)
    {
        $this->filters = array_merge($this->filters, $filters);
    }

}