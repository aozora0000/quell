<?php

declare(strict_types=1);

namespace Querial\Promise\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\AggregatePromiseQuery;

class ThenOrWherePromisesAggregator extends ThenPromisesAggregator
{
    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->match($request)) {
            return $builder;
        }
        $promises = $this->getMatchedPromises($this->promises, $request);

        return $builder->orWhere(function (Builder $query) use ($promises, $request) {
            foreach ($promises as $promise) {
                $promise->resolve($request, $query);
            }
        });
    }
}
