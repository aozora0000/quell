<?php

namespace Querial;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;
use Throwable;

abstract class Quell
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    abstract protected function promise(): ?PromiseInterface;

    /**
     * When Throwable throw
     */
    abstract protected function failed(): callable|Closure|null;

    /**
     * try~catch~finally
     */
    abstract protected function finally(): callable|Closure|null;

    /**
     * When (promises,failed,finally) Not Works.
     */
    protected function default(): callable|Closure|null
    {
        return null;
    }

    /**
     * Builds the pipeline for the given builder.
     *
     * @param  EloquentBuilder|QueryBuilder  $builder The builder instance.
     * @return EloquentBuilder|QueryBuilder The updated builder instance.
     *
     * @throws Throwable
     */
    final public function build(EloquentBuilder|QueryBuilder $builder): EloquentBuilder|QueryBuilder
    {
        $pipeline = new Pipeline($this->request);
        if ($this->promise() !== null) {
            $pipeline->then($this->promise());
        }
        if ($this->failed() !== null) {
            $pipeline->onFailed($this->failed());
        }
        if ($this->finally() !== null) {
            $pipeline->onFinally($this->finally());
        }
        if ($this->default() !== null) {
            $pipeline->onDefault($this->default());
        }

        return $pipeline->build($builder);
    }
}
