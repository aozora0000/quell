<?php

namespace Querial;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;

abstract class Quell
{
    protected Request $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return PromiseInterface|null
     */
    abstract protected function promise(): ?PromiseInterface;

    /**
     * When Throwable throw
     * @return callable|null
     */
    abstract protected function failed(): ?callable;

    /**
     * try~catch~finally
     * @return callable|null
     */
    abstract protected function finally(): ?callable;

    /**
     * When (promises,failed,finally) Not Works.
     * @param Builder $builder
     * @return Builder|null
     */
    protected function default(Builder $builder): ?Builder
    {
        return $builder;
    }

    final public function build(Builder $builder): Builder
    {
        $pipeline = new Pipeline($this->request);
        $pipeline->onDefault(function () use ($builder) {
            return $this->default($builder);
        });
        if($this->promise() !== null) {
            $pipeline->then($this->promise());
        }
        if($this->failed() !== null) {
            $pipeline->onFailed($this->failed());
        }
        if($this->finally() !== null) {
            $pipeline->onFinally($this->finally());
        }
        return $pipeline->build($builder);
    }
}