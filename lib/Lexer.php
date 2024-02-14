<?php

namespace LaMath;

use LaMath\Location;

enum TokenKind
{
    case EOF;
}

class Token
{
    public TokenKind $kind;
    public string $value;
}

class Cursor
{
    private int $index;
    public Location $location;

    public function __construct(private string $buffer)
    {
        $this->index = 0;
        $this->location = new Location(1, 0);
    }

    public function consume(): string
    {
        return $this->buffer[$this->index++];
    }

    public function peek(): string
    {
        return $this->buffer[$this->index];
    }

    public function is_eof()
    {
        return $this->index >= strlen($this->buffer);
    }
}

class Lexer
{
    private Cursor $cursor;

    public function __construct(private string $file_path, string $buffer)
    {
        $this->cursor = new Cursor($buffer);
    }

    public function next_token(): Token
    {
        $tok = new Token();

        $this->skip_whitespace();

        if ($this->cursor->is_eof()) {
            $tok->kind = TokenKind::EOF;

            return $tok;
        }

        return $tok;
    }

    private function skip_whitespace()
    {
        while (!$this->cursor->is_eof() && ctype_space($this->cursor->peek())) $this->cursor->consume();
    }
}
