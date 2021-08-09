<?php

declare (strict_types = 1);

namespace App\Format;

abstract class BaseFormat
{
    protected $data;

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    abstract public function convert(): string;

    public function __toString()
    {
        return $this->convert();
    }
}
