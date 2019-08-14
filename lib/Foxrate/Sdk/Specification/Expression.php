<?php
interface Foxrate_Sdk_Specification_Expression
{
    public function visit(Foxrate_Sdk_Specification_ExpressionVisitor $visitor);
}
