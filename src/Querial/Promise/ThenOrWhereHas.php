<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\AggregatePromiseQuery;
use Querial\Contracts\Support\PromiseQuery;

class ThenOrWhereHas extends PromiseQuery
{
    /**
     * ThenHasRelation constructor.
     */
    public function __construct(protected string $relation, protected ?AggregatePromiseQuery $aggregatePromiseQuery = null) {}

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->aggregatePromiseQuery instanceof AggregatePromiseQuery) {
            return $builder->has($this->relation);
        }

        if (! $this->match($request)) {
            return $builder;
        }

        return $builder->orWhereHas($this->relation, fn (Builder $builder): Builder => $this->aggregatePromiseQuery->resolve($request, $builder));
    }

    public function match(Request $request): bool
    {
        return ! $this->aggregatePromiseQuery instanceof AggregatePromiseQuery || $this->aggregatePromiseQuery->match($request);
    }
}
