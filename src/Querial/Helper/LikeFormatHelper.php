<?php

namespace Querial\Helper;

class LikeFormatHelper extends Enumeration
{
    const FORWORD_MATCH = '%%%s';
    const BACKWORD_MATCH = '%s%%';
    const PARTIAL_MATCH = '%%%s%%';
    const EXACT_MATCH = '%s';

    public function ofValue(string $value): string
    {
        return sprintf($this->of(), $value);
    }
}