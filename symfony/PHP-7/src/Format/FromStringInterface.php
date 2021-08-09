<?php

declare (strict_types = 1);

namespace App\Format;

interface FromStringInterface
{
    public function convertFromString($string);
}
