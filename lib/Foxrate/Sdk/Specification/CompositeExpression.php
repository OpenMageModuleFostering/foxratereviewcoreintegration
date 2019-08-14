<?php

class Foxrate_Sdk_Specification_CompositeExpression implements Foxrate_Sdk_Specification_Expression
{
    const TYPE_AND = 'AND';
    const TYPE_OR = 'OR';

    private $type;

    private $expressions = array();

    public function __construct($type, array $expressions)
    {
        $this->type = $type;

        foreach ($expressions as $expr) {
            if ($expr instanceof Foxrate_Sdk_Specification_Value) {
                throw new \RuntimeException("Values are not supported expressions as children of and/or expressions.");
            }
            if (!($expr instanceof Foxrate_Sdk_Specification_Expression)) {
                throw new \RuntimeException("No expression given to CompositeExpression.");
            }

            $this->expressions[] = $expr;
        }
    }

    public function getExpressionList()
    {
        return $this->expressions;
    }

    public function getType()
    {
        return $this->type;
    }

    public function visit(Foxrate_Sdk_Specification_ExpressionVisitor $visitor)
    {
        return $visitor->walkCompositeExpression($this);
    }
}
