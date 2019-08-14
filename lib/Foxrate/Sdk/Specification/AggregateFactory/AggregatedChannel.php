<?php
class Foxrate_Sdk_Specification_AggregateFactory_AggregatedChannel {

    public static function getRatingAverage()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_AggregatedChannel::NORMALIZE_AVERAGE_TO_FIVE,
            Foxrate_Sdk_Constants_AggregateRuleNames::AVERAGE
        );
    }

    public static function getCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_AggregatedChannel::TOTAL_RATINGS,
            Foxrate_Sdk_Constants_AggregateRuleNames::SUM
        );
    }

    public static function getChannelCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_AggregatedChannel::ID,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }


}