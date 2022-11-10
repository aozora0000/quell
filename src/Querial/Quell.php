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

    abstract protected function promise(): ?PromiseInterface;

    abstract protected function failed(): ?callable;

    abstract protected function finally(): ?callable;

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