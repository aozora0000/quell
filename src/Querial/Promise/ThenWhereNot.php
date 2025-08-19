<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;
use Querial\Contracts\Support\PromiseQuery;

/**
 * 内部のPromise条件を NOT で否定して適用するPromise。
 *
 * 例) new ThenWhereNot(new ThenWhereEqual('name'))
 */
class ThenWhereNot extends PromiseQuery
{
    public function __construct(private PromiseInterface $promise) {}

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        // Laravel 12: whereNot(Closure) が利用可能
        return $builder->whereNot(function (EloquentBuilder $query) use ($request): void {
            $this->promise->resolve($request, $query);
        });
    }

    public function match(Request $request): bool
    {
        return $this->promise->match($request);
    }
}
