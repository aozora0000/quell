<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\AggregatePromiseQuery;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * リレーションの件数で絞り込むPromise。
 */
class ThenWhereRelationCount extends PromiseQuery
{
    protected ScalarTarget $target;

    /**
     * @param string $relation リレーション名
     * @param string $operator 演算子(既定: ">=")
     * @param string|null $inputTarget 件数を受け取るキー
     * @param AggregatePromiseQuery|null $aggregatePromiseQuery リレーション側の追加条件
     */
    public function __construct(
        protected string $relation,
        protected string $operator = '>=',
        ?string $inputTarget = 'count',
        protected ?AggregatePromiseQuery $aggregatePromiseQuery = null
    ) {
        $this->target = new ScalarTarget($inputTarget ?? 'count');
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $count = (int) $this->target->value($request);
        $callback = null;
        if ($this->aggregatePromiseQuery !== null) {
            $callback = fn (Builder $q) => $this->aggregatePromiseQuery->resolve($request, $q);
        }

        return $builder->whereHas($this->relation, $callback, $this->operator, $count);
    }

    public function match(Request $request): bool
    {
        if ($this->aggregatePromiseQuery !== null && ! $this->aggregatePromiseQuery->match($request)) {
            return false;
        }
        return $this->target->is($request);
    }
}
