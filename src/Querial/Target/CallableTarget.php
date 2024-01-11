<?php

namespace Querial\Target;

use Illuminate\Http\Request;
use Querial\Contracts\TargetInterface;

class CallableTarget implements TargetInterface
{
    /**
     * @var callable
     */
    protected $isCallback;

    /**
     * @var callable
     */
    protected $ofCallback;

    public function __construct(callable $isCallback, callable $ofCallback)
    {
        $this->isCallback = $isCallback;
        $this->ofCallback = $ofCallback;
    }

    public function is(Request $request): bool
    {
        return call_user_func($this->isCallback, $request);
    }

    public function value(Request $request): mixed
    {
        return call_user_func($this->ofCallback, $request);
    }
}
