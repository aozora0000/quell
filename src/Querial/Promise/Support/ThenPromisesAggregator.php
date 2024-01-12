<?php

namespace Querial\Promise\Support;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\AggregatePromiseQuery;

class ThenPromisesAggregator extends AggregatePromiseQuery
{
    public function resolveIf(Request $request): bool
    {
        return ! empty($this->resolvedFilter($this->promises, $request));
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->resolveIf($request)) {
            return $builder;
        }
        $promises = $this->resolvedFilter($this->promises, $request);

        foreach ($promises as $promise) {
            $promise->resolve($request, $builder);
        }

        return $builder;
    }
}
