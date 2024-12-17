<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Formatter;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Formatter\LikeFormatter;
use Querial\Promise\Support\ThenOrWherePromisesAggregator;

class ThenWheresOrLike extends PromiseQuery
{
    /**
     * @var string[]
     */
    private array $targets;

    private string $attribute;

    private LikeFormatter|Formatter $formatter;

    public function __construct(string $attribute, array $inputTarget, ?string $table = null, Formatter $formatter = LikeFormatter::PARTIAL_MATCH)
    {
        $this->attribute = $attribute;
        $this->targets = $inputTarget;
        $this->formatter = $formatter;
        $this->table = $table;
    }

    public function match(Request $request): bool
    {
        return $request->filled($this->attribute);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        $promise = array_map(fn (string $target) => new ThenOrWhereLike($this->attribute, $this->attribute, $this->table, $this->formatter), $this->targets);

        return (new ThenOrWherePromisesAggregator($promise))->resolve($request, $builder);
    }
}
