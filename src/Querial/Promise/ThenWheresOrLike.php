<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Formatter;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Formatter\LikeFormatter;
use Querial\Promise\Support\ThenWherePromisesAggregator;

class ThenWheresOrLike extends PromiseQuery
{
    /**
     * @var ThenOrWhereLike[]
     */
    private readonly array $promises;

    public function __construct(
        array $attributes,
        string $target,
        ?string $table = null,
        Formatter $formatter = LikeFormatter::PARTIAL_MATCH)
    {
        $this->promises = array_map(fn (string $attribute) => new ThenOrWhereLike($attribute, $target, $table, $formatter), $attributes);
    }

    public function match(Request $request): bool
    {
        return collect($this->promises)->some(fn (ThenOrWhereLike $target) => $target->match($request));
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        return (new ThenWherePromisesAggregator($this->promises))->resolve($request, $builder);
    }
}
