<?php

namespace LaMath;

class Location
{
    public function __construct(public int $line, public int $column)
    {
    }

    public function display(): string
    {
        return "{$this->line}:{$this->column}";
    }
}
