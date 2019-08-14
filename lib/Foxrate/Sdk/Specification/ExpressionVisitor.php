<?php
abstract class Foxrate_Sdk_Specification_ExpressionVisitor
{
    abstract public function walkComparison(Foxrate_Sdk_Specification_Comparison $comparison);

    abstract public function walkValue(Foxrate_Sdk_Specification_Value $value);

    abstract public function walkCompositeExpression(Foxrate_Sdk_Specification_CompositeExpression $expr);

    public function dispatch(Foxrate_Sdk_Specification_Expression $expr)
    {
        switch (true) {
            case ($expr instanceof Foxrate_Sdk_Specification_Comparison):
                return $this->walkComparison($expr);

            case ($expr instanceof Foxrate_Sdk_Specification_Value):
                return $this->walkValue($expr);

            case ($expr instanceof Foxrate_Sdk_Specification_CompositeExpression):
                return $this->walkCompositeExpression($expr);

            default:
                throw new \RuntimeException("Unknown Expression " . get_class($expr));
        }
    }
}
