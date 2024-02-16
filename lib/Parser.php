<?php

namespace LaMath;

enum Precedence: int
{
    case Lowest = 0;
    case Sum = 1;
    case Product = 2;
    case Exponent = 3;

    public static function from_token(Token $token): Precedence
    {
        switch ($token->kind) {
            case TokenKind::Plus:
            case TokenKind::Minus:
                return Precedence::Sum;
                break;

            case TokenKind::Star:
            case TokenKind::ForwardSlash:
                return Precedence::Product;
                break;

            case TokenKind::DoubleStar:
                return Precedence::Exponent;
                break;

            default:
                return Precedence::Lowest;
                break;
        }
    }
}

class Parser
{
    private Lexer $lexer;

    public function __construct(string $source_code)
    {
        $this->lexer = new Lexer($source_code);
    }

    private function next_token(): Token
    {
        return $this->lexer->next_token();
    }

    private function peek_token(): Token
    {
        return (clone $this->lexer)->next_token();
    }

    private function expect(TokenKind $kind): bool
    {
        if ($this->peek_token()->kind == $kind) {
            $this->next_token();

            return true;
        } else {
            return false;
        }
    }

    public function parse(): Procedure | Error
    {
        $procedure = new Procedure();

        while ($this->peek_token()->kind != TokenKind::EOF) {
            $step = $this->parse_step(Precedence::Lowest);

            if ($step instanceof Error) return $step;

            if (!$this->expect(TokenKind::Semicolon)) return new Error($this->lexer->cursor->location, "expected ';' after each step");

            array_push($procedure->steps, $step);
        }

        return $procedure;
    }

    public function parse_step(Precedence $precedence): Step | Error
    {
        $lhs = $this->parse_unary_operation();

        if ($lhs instanceof Error) return $lhs;

        while ($this->peek_token()->kind != TokenKind::EOF && Precedence::from_token($this->peek_token())->value > $precedence->value) {
            $lhs = $this->parse_binary_operation($lhs);

            if ($lhs instanceof Error) return $lhs;
        }

        return $lhs;
    }

    public function parse_unary_operation(): Step | Error
    {
        switch ($this->peek_token()->kind) {
            case TokenKind::Number:
                return $this->parse_number();

            case TokenKind::Invalid:
                return new Error($this->lexer->cursor->location, "invalid token '" . $this->peek_token()->value . "'");

            default:
                return new Error($this->lexer->cursor->location, "expected a step");
        }
    }

    public function parse_number(): Step | Error
    {
        $value = $this->next_token()->value;

        $location = clone $this->lexer->cursor->location;

        if (!is_numeric($value)) return new Error($location, "invalid number");

        return new Number(floatval($value), $location);
    }

    public function parse_binary_operation(Step $lhs): Step | Error
    {
        $operator_token = $this->next_token();

        $operator_location = clone $this->lexer->cursor->location;

        $operator = BinaryOperator::from_token($operator_token);

        $rhs = $this->parse_step(Precedence::from_token($operator_token));

        if ($rhs instanceof Error) return $rhs;

        return new BinaryOperation($lhs, $operator, $rhs, $operator_location);
    }
}
