<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;

class ThenCallable extends PromiseQuery
{
    /**
     * @var callable
     */
    protected $resolveIf;

    /**
     * @var callable
     */
    protected $resolve;

    /**
     * @param  callable(Request): bool  $resolveIf
     * @param  callable(Request, EloquentBuilder): EloquentBuilder  $resolve
     */
    public function __construct(callable $resolveIf, callable $resolve)
    {
        $this->resolveIf = $resolveIf;
        $this->resolve = $resolve;
    }

    public function resolveIf(Request $request): bool
    {
        return call_user_func($this->resolveIf, $request);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        return call_user_func($this->resolve, $request, $builder);
    }
}
