<?php

declare(strict_types=1);

namespace Querial\Promise\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ThenWherePromisesAggregator extends ThenPromisesAggregator
{
    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $promises = $this->getMatchedPromises($this->promises, $request);

        return $builder->where(function (Builder $query) use ($promises, $request): void {
            foreach ($promises as $promise) {
                $promise->resolve($request, $query);
            }
        });
    }
}
