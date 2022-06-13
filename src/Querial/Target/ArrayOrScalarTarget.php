<?php declare(strict_types=1);
namespace Querial\Target;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Querial\Contracts\TargetInterface;

class ArrayOrScalarTarget implements TargetInterface
{
    /**
     * @var string
     */
    protected string $target;

    public function __construct(string $target, string $postfix = '')
    {
        $this->target = $target . $postfix;
    }

    public function is(Request $request): bool
    {
        return
            $request->has($this->target) &&
            !empty($request->input($this->target)) &&
            (is_scalar($request->input($this->target)) || is_array($request->input($this->target)));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function of(Request $request)
    {
        if (!$this->is($request)) {
            return [];
        }

        return is_array($request->input($this->target, [])) ?
            Arr::flatten($request->input($this->target, [])) :
            [$request->input($this->target, [])];
    }
}
