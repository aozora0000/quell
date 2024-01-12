<?php

namespace Querial\Promise\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;

class IfCallable implements PromiseInterface
{
    /**
     * @var callable|Closure
     */
    private $closure;

    private PromiseInterface $promise;

    /**
     * @param  callable(Request $request): bool|Closure(Request $request):bool  $closure
     */
    public function __construct(callable|Closure $closure, PromiseInterface $promise)
    {
        $this->closure = $closure;
        $this->promise = $promise;
    }

    public function match(Request $request): bool
    {
        return call_user_func($this->closure, $request) && $this->promise->match($request);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        return $this->promise->resolve($request, $builder);
    }
}
