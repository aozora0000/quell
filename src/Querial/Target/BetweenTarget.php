<?php

declare(strict_types=1);

namespace Querial\Target;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Querial\Contracts\TargetInterface;

class BetweenTarget implements TargetInterface
{
    protected TargetInterface $maxTarget;

    protected TargetInterface $minTarget;

    /**
     * BetweenTarget constructor.
     */
    public function __construct(TargetInterface $maxTarget, TargetInterface $minTarget)
    {
        $this->maxTarget = $maxTarget;
        $this->minTarget = $minTarget;
    }

    public function min(): TargetInterface
    {
        return $this->minTarget;
    }

    public function max(): TargetInterface
    {
        return $this->maxTarget;
    }

    public function is(Request $request): bool
    {
        return $this->maxTarget->is($request) && $this->minTarget->is($request);
    }

    /**
     * TODO: min(), max()を比較並べ替えした後に取れるようにした方が良かった
     *
     * @return string[]
     */
    public function value(Request $request)
    {
        return array_values(Arr::sort([
            $this->maxTarget->value($request),
            $this->minTarget->value($request),
        ]));
    }
}
