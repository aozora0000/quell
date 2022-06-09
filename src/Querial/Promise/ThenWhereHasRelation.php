<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 07:37
 */
namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;
use Querial\Contracts\Support\CreateAttributeFromTable;
use Querial\Contracts\Support\PromiseAggregateImpl;

class ThenWhereHasRelation implements PromiseInterface
{
    use CreateAttributeFromTable;

    /**
     * @var string
     */
    protected string $relation;

    /**
     * @var PromiseAggregateImpl | null
     */
    protected ?PromiseAggregateImpl $aggregator;

    /**
     * ThenHasRelation constructor.
     *
     * @param string                    $relation
     * @param PromiseAggregateImpl|null $aggregator
     */
    public function __construct(string $relation, ?PromiseAggregateImpl $aggregator)
    {
        $this->relation   = $relation;
        $this->aggregator = $aggregator;
    }

    /**
     * @param Request $request
     * @param Builder $builder
     *
     * @return Builder
     */
    public function resolve(Request $request, Builder $builder): Builder
    {
        if ($this->aggregator === null) {
            return $builder->has($this->relation);
        }
        if (!$this->resolveIf($request)) {
            return $builder;
        }

        return $builder->whereHas($this->relation, function (Builder $builder) use ($request) {
            return $this->aggregator->resolve($request, $builder);
        });
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function resolveIf(Request $request): bool
    {
        return $this->aggregator === null || $this->aggregator->resolveIf($request);
    }
}
