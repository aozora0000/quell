<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 06:57
 */

namespace Querial\Promise;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Target\BetweenTarget;
use Querial\Target\DatetimeTarget;

class ThenWhereBetweenDays extends ThenWhereBetween
{
    public function __construct(string $attribute, ?string $inputTarget = null, string $format = 'Y-m-d H:i:s', string $minPostfix = '_min', string $maxPostfix = '_max')
    {
        $this->attribute = $attribute;
        $target = $inputTarget ?? $attribute;
        $this->target = new BetweenTarget(new DatetimeTarget($format, $target.$maxPostfix), new DatetimeTarget($format, $target.$minPostfix));
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->resolveIf($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        if ($this->target->is($request)) {
            [$min, $max] = $this->target->value($request);

            return match (true) {
                $max instanceof Carbon && $min instanceof Carbon => $builder->whereBetween($attribute, [$min->startOfDay(), $max->endOfDay()]),
                $max instanceof Carbon => $builder->whereBetween($attribute, [$min, $max->endOfDay()]),
                $min instanceof Carbon => $builder->whereBetween($attribute, [$min->startOfDay(), $max]),
                default => $builder->whereBetween($attribute, [$min, $max]),
            };
        }
        if ($this->target->max()->is($request)) {
            $max = $this->target->max();

            return match (true) {
                $max instanceof DatetimeTarget => $builder->where($attribute, '<=', $max->value($request)->endOfDay()),
                default => $builder->where($attribute, '<=', $max->value($request)),
            };
        }
        if ($this->target->min()->is($request)) {
            $min = $this->target->min();

            return match (true) {
                $min instanceof DatetimeTarget => $builder->where($attribute, '>=', $min->value($request)->startOfDay()),
                default => $builder->where($attribute, '>=', $min->value($request)),
            };
        }

        return $builder;
    }
}
