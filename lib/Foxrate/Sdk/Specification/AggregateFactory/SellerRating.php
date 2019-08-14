<?php
class Foxrate_Sdk_Specification_AggregateFactory_SellerRating {

    public static function getRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }

    public static function getCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::ALL,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }

    public static function getRecommendCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::RECOMMEND,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }

    public static function getProductDetailsRatingCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::PRODUCT_DETAILS_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }

    public static function getOrderProcessRatingCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::ORDER_PROCESS_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }

    public static function getDeliveryRatingCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::DELIVERY_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }

    public static function getCustomerServiceRatingCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::CUSTOMER_SERVICE_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }

    public static function getProductDetailsRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::PRODUCT_DETAILS_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }

    public static function getOrderProcessRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::ORDER_PROCESS_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }

    public static function getDeliveryRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::DELIVERY_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }

    public static function getCustomerServiceRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_SellerRating::CUSTOMER_SERVICE_RATING,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }
}