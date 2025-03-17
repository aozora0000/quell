<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 06:57
 */

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\BetweenTarget;
use Querial\Target\ScalarTarget;

class ThenWhereBetween extends PromiseQuery
{
    protected BetweenTarget $target;

    /**
     * FactoryInterface constructor.
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        string $minPostfix = '_min',
        string $maxPostfix = '_max')
    {
        $target = $inputTarget ?? $attribute;
        $this->target = new BetweenTarget(new ScalarTarget($target, $maxPostfix), new ScalarTarget($target, $minPostfix));
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return match (true) {
            ! $this->match($request) => $builder,
            $this->target->is($request) => $builder->whereBetween($attribute, $this->target->value($request)),
            $this->target->max()->is($request) => $builder->where($attribute, '<=', $this->target->max()->value($request)),
            $this->target->min()->is($request) => $builder->where($attribute, '>=', $this->target->min()->value($request)),
            default => $builder,
        };
    }

    public function match(Request $request): bool
    {
        return
            $this->target->is($request) ||
            $this->target->max()->is($request) ||
            $this->target->min()->is($request);
    }
}
