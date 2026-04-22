<?php

declare(strict_types=1);

namespace Querial\Contracts;

interface Formatter
{
    public function format(string $value): string;
}
