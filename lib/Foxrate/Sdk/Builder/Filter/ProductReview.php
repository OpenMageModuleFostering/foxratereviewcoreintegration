<?php

class Foxrate_Sdk_Builder_Filter_ProductReview{

    private $filters;

    public function setSuspicious($value)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_ProductReview_Suspicious($value);
    }

    public function setDisplay($value)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_ProductReview_Display($value);
    }

    public function setRating($rating)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_ProductReview_Rating($rating);
    }

    public function setPriceRating($rating)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_ProductReview_PriceRating($rating);
    }

    public function setProductIDList($productIDList)
    {
        $this->filters[] = new Foxrate_Sdk_Filter_ProductReview_ProductIdList($productIDList);
    }

    public function setQualityRatingNot($rating)
    {
        $ratingFilter = new Foxrate_Sdk_Filter_ProductReview_QualityRating();
        $ratingFilter->not($rating);
        $this->filters[] = $ratingFilter;
    }

    public function setPriceRatingNot($rating)
    {
        $ratingFilter = new Foxrate_Sdk_Filter_ProductReview_PriceRating();
        $ratingFilter->not($rating);
        $this->filters[] = $ratingFilter;
    }

    public function setRecommend($value)
    {
        $recommendFilter = new Foxrate_Sdk_Filter_ProductReview_Recommend($value);
        $recommendFilter->is($value);
        $this->filters[] = $recommendFilter;
    }

    public function setRecommendNot($value)
    {
        $recommendFilter = new Foxrate_Sdk_Filter_ProductReview_Recommend($value);
        $recommendFilter->not($value);
        $this->filters[] = $recommendFilter;
    }

    public function getFilters()
    {
        return $this->filters;
    }


}