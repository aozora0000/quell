<?php

namespace Querial\Formatter;

use Querial\Contracts\Enumeration;
use Querial\Contracts\Formatter;

class LikeFormatter extends Enumeration implements Formatter
{
    const FORWORD_MATCH = '%%%s';
    const BACKWORD_MATCH = '%s%%';
    const PARTIAL_MATCH = '%%%s%%';
    const EXACT_MATCH = '%s';

    public function format(string $value): string
    {
        return sprintf($this->of(), $value);
    }
}