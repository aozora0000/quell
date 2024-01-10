<?php

declare(strict_types=1);

namespace Querial\Promise\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\AggregatePromiseQuery;

class ThenWherePromisesAggregator extends AggregatePromiseQuery
{
    public function resolveIf(Request $request): bool
    {
        return ! empty($this->resolvedFilter($this->promises, $request));
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->resolveIf($request)) {
            return $builder;
        }
        $promises = $this->resolvedFilter($this->promises, $request);

        return $builder->where(function (Builder $query) use ($promises, $request) {
            foreach ($promises as $promise) {
                $promise->resolve($request, $query);
            }
        });
    }
}
