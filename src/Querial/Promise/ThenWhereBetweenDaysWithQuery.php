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
use Querial\Contracts\Support\CreateAttributeFromTable;
use Querial\Target\BetweenTarget;
use Querial\Target\DatetimeTarget;

class ThenWhereBetweenDaysWithQuery extends ThenWhereBetweenWithQuery
{

    public function __construct(string $attribute, ?string $inputTarget = null, string $minPostfix = '_min', string $maxPostfix = '_max')
    {
        $this->attribute     = $attribute;
        $target              = $inputTarget ?? $attribute;
        $this->target        = new BetweenTarget(new DatetimeTarget($target, $minPostfix), new DatetimeTarget($target, $maxPostfix));
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        dd($this->target->is($request),
            $this->target->max()->is($request),
            $this->target->min()->is($request));
        if (!$this->resolveIf($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        if ($this->target->is($request)) {
            [$min, $max] = $this->target->of($request);
            dd($min, $max);
            return $builder->whereBetween($attribute, [$min->startOfDay(), $max->endOfDay()]);
        }
        if ($this->target->max()->is($request)) {
            $max = $this->target->max();
            switch (true) {
                case $max instanceof DatetimeTarget:
                    $builder->where($attribute, '<=', $max->of($request)->endOfDay());
                    break;
                default:
                    $builder->where($attribute, '<=', $max->of($request));
                    break;
            }
        }
        if ($this->target->min()->is($request)) {
            $min = $this->target->max();
            switch (true) {
                case $min instanceof DatetimeTarget:
                    $builder->where($attribute, '>=', $min->of($request)->startOfDay());
                    break;
                default:
                    $builder->where($attribute, '>=', $min->of($request));
                    break;
            }
        }

        return $builder;
    }
}
