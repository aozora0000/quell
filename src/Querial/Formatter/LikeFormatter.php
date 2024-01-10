<?php

namespace Querial\Formatter;

use Querial\Contracts\Formatter;

enum LikeFormatter implements Formatter
{
    case FORWARD_MATCH;
    case BACKWARD_MATCH;
    case PARTIAL_MATCH;
    case EXACT_MATCH;

    public function format(string $value): string
    {
        return match ($this) {
            self::FORWARD_MATCH => $value.'%',
            self::BACKWARD_MATCH => '%'.$value,
            self::PARTIAL_MATCH => '%'.$value.'%',
            self::EXACT_MATCH => $value,
        };
    }
}
