<?php

namespace LaMath;

class Error
{
    public string $file_path;

    public function __construct(public Location $location, public string $message)
    {
    }

    public function display(): string
    {
        return "{$this->file_path}:{$this->location->display()}: {$this->message}";
    }
}
