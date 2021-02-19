<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 06:57
 */
namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Target\BetweenTarget;
use Querial\Target\DatetimeScalarTarget;

class ThenWhereBetweenDaysWithQuery extends ThenWhereBetweenWithQuery
{
    public function __construct(string $attribute, ?string $inputTarget = null, string $minPostfix = '_min', string $maxPostfix = '_max')
    {
        $this->attribute     = $attribute;
        $target              = $inputTarget ?? $attribute;
        $this->target        = new BetweenTarget(new DatetimeScalarTarget($target, $minPostfix), new DatetimeScalarTarget($target, $maxPostfix));
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (!$this->resolveIf($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        if ($this->target->isTarget($request)) {
            [$min, $max] = $this->target->getTarget($request);

            return $builder->whereBetween($attribute, [$min->startOfDay(), $max->endOfDay()]);
        }
        if ($this->target->max()->isTarget($request)) {
            $builder->where($attribute, '<=', $this->target->max()->getTarget($request)->endOfDay());
        }
        if ($this->target->min()->isTarget($request)) {
            $builder->where($attribute, '>=', $this->target->min()->getTarget($request)->startOfDay());
        }

        return $builder;
    }
}
