<?php

class Foxrate_Sdk_Specification_ExpressionBuilder
{
    public function andX($x = null)
    {
        return new Foxrate_Sdk_Specification_CompositeExpression(Foxrate_Sdk_Specification_CompositeExpression::TYPE_AND, func_get_args());
    }

    public function orX($x = null)
    {
        return new Foxrate_Sdk_Specification_CompositeExpression(Foxrate_Sdk_Specification_CompositeExpression::TYPE_OR, func_get_args());
    }

    public function eq($field, $value)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::EQ, new Foxrate_Sdk_Specification_Value($value));
    }

    public function gt($field, $value)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::GT, new Foxrate_Sdk_Specification_Value($value));
    }

    public function lt($field, $value)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::LT, new Foxrate_Sdk_Specification_Value($value));
    }

    public function gte($field, $value)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::GTE, new Foxrate_Sdk_Specification_Value($value));
    }

    public function lte($field, $value)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::LTE, new Foxrate_Sdk_Specification_Value($value));
    }

    public function neq($field, $value)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::NEQ, new Foxrate_Sdk_Specification_Value($value));
    }

    public function isNull($field)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::EQ, new Foxrate_Sdk_Specification_Value(null));
    }

    public function in($field, array $values)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::IN, new Foxrate_Sdk_Specification_Value($values));
    }

    public function notIn($field, array $values)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::NIN, new Foxrate_Sdk_Specification_Value($values));
    }

    public function contains($field, $value)
    {
        return new Foxrate_Sdk_Specification_Comparison($field, Foxrate_Sdk_Specification_Comparison::CONTAINS, new Foxrate_Sdk_Specification_Value($value));
    }
}
