<?php

namespace Querial\Formatter;

use Querial\Contracts\Formatter;

enum LikeFormatter: string implements Formatter
{
    case FORWORD_MATCH = '%%%s';
    case BACKWORD_MATCH = '%s%%';
    case PARTIAL_MATCH = '%%%s%%';
    case EXACT_MATCH = '%s';

    public function format(string $value): string
    {
        return sprintf($this->value, $value);
    }
}
