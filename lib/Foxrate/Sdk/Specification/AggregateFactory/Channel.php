<?php
class Foxrate_Sdk_Specification_AggregateFactory_Channel {

    public static function getChannelCount()
    {
        return new Foxrate_Sdk_Specification_Aggregate(
            Foxrate_Sdk_Constants_FieldName_Channel::ID,
            Foxrate_Sdk_Constants_AggregateRuleNames::COUNT
        );
    }


}