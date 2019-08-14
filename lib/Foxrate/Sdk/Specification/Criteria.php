<?php

class Foxrate_Sdk_Specification_Criteria
{
    const ASC  = 'ASC';

    const DESC = 'DESC';

    private static $expressionBuilder;

    private $expression;

    private $orderings;

    private $firstResult;

    private $maxResults;

    public static function create()
    {
        return new static();
    }

    public static function expr()
    {
        if (self::$expressionBuilder === null) {
            self::$expressionBuilder = new Foxrate_Sdk_Specification_ExpressionBuilder();
        }
        return self::$expressionBuilder;
    }

    public function __construct(Foxrate_Sdk_Specification_Expression $expression = null, array $orderings = null, $firstResult = null, $maxResults = null)
    {
        $this->expression  = $expression;
        $this->orderings   = $orderings;
        $this->firstResult = $firstResult;
        $this->maxResults  = $maxResults;
    }

    public function where(Foxrate_Sdk_Specification_Expression $expression)
    {
        $this->expression = $expression;
        return $this;
    }

    public function andWhere(Foxrate_Sdk_Specification_Expression $expression)
    {
        if ($this->expression === null) {
            return $this->where($expression);
        }

        $this->expression = new Foxrate_Sdk_Specification_CompositeExpression(Foxrate_Sdk_Specification_CompositeExpression::TYPE_AND, array(
            $this->expression, $expression
        ));

        return $this;
    }

    public function orWhere(Foxrate_Sdk_Specification_Expression $expression)
    {
        if ($this->expression === null) {
            return $this->where($expression);
        }

        $this->expression = new Foxrate_Sdk_Specification_CompositeExpression(Foxrate_Sdk_Specification_CompositeExpression::TYPE_OR, array(
            $this->expression, $expression
        ));

        return $this;
    }

    public function getWhereExpression()
    {
        return $this->expression;
    }

    public function getOrderings()
    {
        return $this->orderings;
    }

    public function orderBy(array $orderings)
    {
        $this->orderings = $orderings;
        return $this;
    }

    public function getFirstResult()
    {
        return $this->firstResult;
    }

    public function setFirstResult($firstResult)
    {
        $this->firstResult = $firstResult;
        return $this;
    }

    public function getMaxResults()
    {
        return $this->maxResults;
    }

    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
        return $this;
    }
}
