<?php
class Foxrate_Sdk_Specification_Aggregate
{
    private $fieldName;
    private $rule;

    public function __construct($fieldName, $rule)
    {
        $this->fieldName = $fieldName;
        $this->rule = $rule;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function getRule()
    {
        return $this->rule;
    }
}