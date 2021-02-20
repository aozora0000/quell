<?php declare(strict_types=1);
namespace Querial\Target;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
     *
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

    public function isTarget(Request $request): bool
    {
        return $this->maxTarget->isTarget($request) && $this->minTarget->isTarget($request);
    }

    /**
     * @param Request $request
     *
     * @return Carbon[]
     */
    public function getTarget(Request $request)
    {
        return array_values(Arr::sort([
            $this->maxTarget->getTarget($request),
            $this->minTarget->getTarget($request),
        ]));
    }
}
