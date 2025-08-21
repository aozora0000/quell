<?php

namespace Querial\Helper;

class Str
{
    public static function isTruthy($val): bool
    {
        return is_string($val) && in_array(strtolower($val), ['1', 'true', 'on', 'yes'], true);
    }

    public static function isFalsy($val): bool
    {
        return is_string($val) && in_array(strtolower($val), ['0', 'false', 'off', 'no'], true);
    }
}