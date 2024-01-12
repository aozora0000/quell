<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 07:37
 */

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\AggregatePromiseQuery;
use Querial\Contracts\Support\PromiseQuery;

class ThenWhereHasRelation extends PromiseQuery
{
    protected string $relation;

    protected ?AggregatePromiseQuery $aggregator;

    /**
     * ThenHasRelation constructor.
     */
    public function __construct(string $relation, ?AggregatePromiseQuery $aggregator = null)
    {
        $this->relation = $relation;
        $this->aggregator = $aggregator;
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if ($this->aggregator === null) {
            return $builder->has($this->relation);
        }
        if (! $this->match($request)) {
            return $builder;
        }

        return $builder->whereHas($this->relation, function (Builder $builder) use ($request) {
            return $this->aggregator->resolve($request, $builder);
        });
    }

    public function match(Request $request): bool
    {
        return $this->aggregator === null || $this->aggregator->match($request);
    }
}
