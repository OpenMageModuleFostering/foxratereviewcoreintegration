<?php
class Foxrate_Sdk_Specification_Comparison implements Foxrate_Sdk_Specification_Expression
{
    const EQ        = '=';
    const NEQ       = '<>';
    const LT        = '<';
    const LTE       = '<=';
    const GT        = '>';
    const GTE       = '>=';
    const IS        = '='; // no difference with EQ
    const IN        = 'IN';
    const NIN       = 'NIN';
    const CONTAINS  = 'CONTAINS';

    private $field;

    private $op;

    private $value;

    public function __construct($field, $operator, $value)
    {
        if (!($value instanceof Foxrate_Sdk_Specification_Value)) {
            $value = new Foxrate_Sdk_Specification_Value($value);
        }

        $this->field = $field;
        $this->op = $operator;
        $this->value = $value;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getOperator()
    {
        return $this->op;
    }

    public function visit(Foxrate_Sdk_Specification_ExpressionVisitor $visitor)
    {
        return $visitor->walkComparison($this);
    }
}
