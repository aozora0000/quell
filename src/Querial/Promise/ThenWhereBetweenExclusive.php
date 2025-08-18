<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\BetweenTarget;
use Querial\Target\ScalarTarget;

/**
 * min/maxの排他的な範囲(> と <)で絞り込むPromise。
 */
class ThenWhereBetweenExclusive extends PromiseQuery
{
    protected BetweenTarget $target;

    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        string $minPostfix = '_min',
        string $maxPostfix = '_max',
        ?string $table = null
    ) {
        $target = $inputTarget ?? $this->attribute;
        $this->target = new BetweenTarget(new ScalarTarget($target, $maxPostfix), new ScalarTarget($target, $minPostfix));
        $this->table = $table;
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return match (true) {
            ! $this->match($request) => $builder,
            $this->target->is($request) => $builder->where($attribute, '>', $this->target->min()->value($request))
                ->where($attribute, '<', $this->target->max()->value($request)),
            $this->target->max()->is($request) => $builder->where($attribute, '<', $this->target->max()->value($request)),
            $this->target->min()->is($request) => $builder->where($attribute, '>', $this->target->min()->value($request)),
            default => $builder,
        };
    }

    public function match(Request $request): bool
    {
        if ($this->target->is($request)) {
            return true;
        }
        if ($this->target->max()->is($request)) {
            return true;
        }
        return $this->target->min()->is($request);
    }
}
