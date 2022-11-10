<?php

namespace Querial\Contracts;

interface Formatter
{
    public function format(string $value): string;
}