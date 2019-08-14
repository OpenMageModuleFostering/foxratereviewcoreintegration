<?php
class Foxrate_Sdk_Specification_AggregateFactory_ProductReview {

    public static function getRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_ProductReview::RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }

    public static function getQualityRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_ProductReview::QUALITY_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }


    public static function getQualityRatingCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_ProductReview::QUALITY_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }


    public static function getPriceRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_ProductReview::PRICE_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }


    public static function getPriceRatingCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_ProductReview::PRICE_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }

    public static function getRecommendCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_ProductReview::RECOMMEND,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }

    public static function getCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_ProductReview::ALL,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }
}