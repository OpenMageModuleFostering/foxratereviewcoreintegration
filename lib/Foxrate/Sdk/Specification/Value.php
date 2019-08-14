<?php

class Foxrate_Sdk_Specification_Value implements Foxrate_Sdk_Specification_Expression
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function visit(Foxrate_Sdk_Specification_ExpressionVisitor $visitor)
    {
        return $visitor->walkValue($this);
    }
}
