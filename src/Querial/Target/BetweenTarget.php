<?php declare(strict_types = 1);

namespace Querial\Target;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Querial\Contracts\TargetInterface;

class BetweenTarget implements TargetInterface
{
    /**
     * @var ScalarTarget
     */
    protected ScalarTarget $maxTarget;
    /**
     * @var ScalarTarget
     */
    protected ScalarTarget $minTarget;

    /**
     * BetweenTarget constructor.
     * @param ScalarTarget $maxTarget
     * @param ScalarTarget $minTarget
     */
    public function __construct(ScalarTarget $maxTarget, ScalarTarget $minTarget)
    {
        $this->maxTarget = $maxTarget;
        $this->minTarget = $minTarget;
    }

    /**
     * @return ScalarTarget
     */
    public function min(): ScalarTarget
    {
        return $this->minTarget;
    }

    /**
     * @return ScalarTarget
     */
    public function max(): ScalarTarget
    {
        return $this->maxTarget;
    }

    public function is(Request $request): bool
    {
        return $this->maxTarget->is($request) && $this->minTarget->is($request);
    }

    /**
     * TODO: min(), max()を比較並べ替えした後に取れるようにした方が良かった
     * @param Request $request
     * @return string[]
     */
    public function of(Request $request)
    {
        return array_values(Arr::sort([
            $this->maxTarget->of($request),
            $this->minTarget->of($request),
        ]));
    }
}
