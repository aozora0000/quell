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
use Querial\Target\DatetimeTarget;

class ThenWhereBetweenDaysWithQuery extends ThenWhereBetweenWithQuery
{
    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->resolveIf($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        if ($this->target->is($request)) {
            [$min, $max] = $this->target->value($request);
            switch (true) {
                case $max instanceof Carbon && $min instanceof Carbon:
                    return $builder->whereBetween($attribute, [$min->startOfDay(), $max->endOfDay()]);
                case $max instanceof Carbon:
                    return $builder->whereBetween($attribute, [$min, $max->endOfDay()]);
                case $min instanceof Carbon:
                    return $builder->whereBetween($attribute, [$min->startOfDay(), $max]);
                default:
                    return $builder->whereBetween($attribute, [$min, $max]);
            }
        }
        if ($this->target->max()->is($request)) {
            $max = $this->target->max();
            switch (true) {
                case $max instanceof DatetimeTarget:
                    $builder->where($attribute, '<=', $max->value($request)->endOfDay());
                    break;
                default:
                    $builder->where($attribute, '<=', $max->value($request));
                    break;
            }
        }
        if ($this->target->min()->is($request)) {
            $min = $this->target->min();
            switch (true) {
                case $min instanceof DatetimeTarget:
                    $builder->where($attribute, '>=', $min->value($request)->startOfDay());
                    break;
                default:
                    $builder->where($attribute, '>=', $min->value($request));
                    break;
            }
        }

        return $builder;
    }
}
