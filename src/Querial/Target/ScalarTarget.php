<?php

declare(strict_types=1);

namespace Querial\Target;

use Illuminate\Http\Request;
use Querial\Contracts\TargetInterface;

class ScalarTarget implements TargetInterface
{
    protected string $target;

    public function __construct(string $target, string $postfix = '')
    {
        $this->target = $target.$postfix;
    }

    public function is(Request $request): bool
    {
        return
            $request->filled($this->target) &&
            is_scalar($request->input($this->target));
    }

    public function value(Request $request): string
    {
        return $request->str($this->target, '')->value();
    }
}
