<?php declare(strict_types=1);
namespace Querial\Target;

use Illuminate\Http\Request;

class ScalarTarget implements TargetInterface
{
    /**
     * @var string
     */
    protected string $target;

    public function __construct(string $target, string $postfix = '')
    {
        $this->target = $target . $postfix;
    }

    public function isTarget(Request $request): bool
    {
        return
            $request->has($this->target) &&
            !empty($request->input($this->target)) &&
            is_scalar($request->input($this->target));
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getTarget(Request $request)
    {
        return $request->input($this->target, '');
    }
}
