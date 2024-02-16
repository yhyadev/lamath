<?php

namespace LaMath;

enum TokenKind
{
    case Number;

    case Plus;
    case Minus;
    case Star;
    case DoubleStar;
    case ForwardSlash;

    case Semicolon;

    case Invalid;
    case EOF;
}

class Token
{
    public function __construct(public TokenKind $kind, public string $value)
    {
    }
}

class Cursor
{
    public int $index;
    public Location $location;

    public function __construct(private string $buffer)
    {
        $this->index = 0;
        $this->location = new Location(1, 0);
    }

    public function __clone()
    {
        $this->location = clone $this->location;
    }

    public function consume(): string
    {
        $character = $this->buffer[$this->index++];

        if ($character === "\n") {
            $this->location->line += 1;
            $this->location->column = 1;
        } else {
            $this->location->column += 1;
        }

        return $character;
    }

    public function peek(): string
    {
        return $this->buffer[$this->index];
    }

    public function is_eof(): bool
    {
        return $this->index >= strlen($this->buffer);
    }
}

class Lexer
{
    public Cursor $cursor;

    public function __construct(string $buffer)
    {
        $this->cursor = new Cursor($buffer);
    }

    public function __clone()
    {
        $this->cursor = clone $this->cursor;
    }

    public function next_token(): Token
    {
        $token = new Token(TokenKind::EOF, "");

        $this->skip_whitespace();

        if ($this->cursor->is_eof()) return $token;

        $character = $this->cursor->consume();

        switch ($character) {
            case "+":
                $token = new Token(TokenKind::Plus, $character);

                break;

            case "-":
                $token = new Token(TokenKind::Minus, $character);

                break;

            case "*":
                if (!$this->cursor->is_eof() && $this->cursor->peek() === "*") {
                    $this->cursor->consume();

                    $token = new Token(TokenKind::DoubleStar, "**");
                } else {
                    $token = new Token(TokenKind::Star, $character);
                }

                break;

            case "/":
                $token = new Token(TokenKind::ForwardSlash, $character);

                break;

            case ";":
                $token = new Token(TokenKind::Semicolon, $character);

                break;

            default:
                if (ctype_digit($character)) {
                    $token = $this->tokenize_number($character);
                } else {
                    $token = new Token(TokenKind::Invalid, $character);
                }

                break;
        }

        return $token;
    }

    private function skip_whitespace()
    {
        while (!$this->cursor->is_eof() && ctype_space($this->cursor->peek())) $this->cursor->consume();
    }

    private function tokenize_number(string $first_character): Token
    {
        $literal = $first_character;

        while (!$this->cursor->is_eof() && (ctype_digit($this->cursor->peek()) || $this->cursor->peek() === ".")) {
            $literal = $literal . $this->cursor->consume();
        }

        return new Token(TokenKind::Number, $literal);
    }
}
