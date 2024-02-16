<?php

namespace LaMath;

class Procedure
{
    public function __construct(public array $steps = [])
    {
    }
}

interface Step
{
}

class Number implements Step
{
    public function __construct(public float $value, public Location $location)
    {
    }
}

class BinaryOperation implements Step
{
    public function __construct(public Step $lhs, public BinaryOperator $operator, public Step $rhs, public Location $location)
    {
    }
}

enum BinaryOperator
{
    case Plus;
    case Minus;
    case Star;
    case DoubleStar;
    case ForwardSlash;

    public static function from_token(Token $token): BinaryOperator
    {
        switch ($token->kind) {
            case TokenKind::Plus:
                return BinaryOperator::Plus;

            case TokenKind::Minus:
                return BinaryOperator::Minus;

            case TokenKind::Star:
                return BinaryOperator::Star;

            case TokenKind::DoubleStar:
                return BinaryOperator::DoubleStar;

            case TokenKind::ForwardSlash:
                return BinaryOperator::ForwardSlash;
        }
    }
}
