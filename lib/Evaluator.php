<?php

namespace LaMath;

use LaMath\AST;
use LaMath\Error;

interface Value
{
    public function display(): string;
}

class Number implements Value
{
    public function __construct(public float $value)
    {
    }

    public function display(): string
    {
        return $this->value;
    }
}

class Evaluator
{
    public function evaluate(AST\Procedure $procedure): array | Error
    {
        $values = [];

        foreach ($procedure->steps as $step) {
            $value = $this->evaluate_step($step);

            if ($value instanceof Error) return $value;

            array_push($values, $value);
        }

        return $values;
    }

    private function evaluate_step(AST\Step $step): Value | Error
    {
        if ($step instanceof AST\Number) {
            return new Number($step->value);
        } elseif ($step instanceof AST\BinaryOperation) {
            $lhs = $this->evaluate_step($step->lhs);

            if ($lhs instanceof Error) return $lhs;

            $rhs = $this->evaluate_step($step->rhs);

            if ($rhs instanceof Error) return $rhs;

            if ($lhs instanceof Number && $rhs instanceof Number) {
                return $this->evaluate_number_binary_operation($lhs, $step->operator, $rhs);
            } else {
                return new Error($step->location, "could not evaluate binary operation");
            }
        }
    }

    private function evaluate_number_binary_operation(Number $lhs, AST\BinaryOperator $operator, Number $rhs): Value {
        switch ($operator) {
            case AST\BinaryOperator::Plus:
                return new Number($lhs->value + $rhs->value);

                break;

            case AST\BinaryOperator::Minus:
                return new Number($lhs->value - $rhs->value);
                
                break;
                
            case AST\BinaryOperator::Star:
                return new Number($lhs->value * $rhs->value);

                break;

            case AST\BinaryOperator::DoubleStar:
                return new Number($lhs->value ** $rhs->value);

                break;

            case AST\BinaryOperator::ForwardSlash:
                return new Number($lhs->value / $rhs->value);

                break;
        }
    }
}
