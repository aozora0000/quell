<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\BetweenTarget;
use Querial\Target\ScalarTarget;

/**
 * 集計結果などに対して HAVING 句で範囲指定を行う Promise。
 *
 * - min/max 両方: HAVING attribute BETWEEN min AND max
 * - min のみ: HAVING attribute >= min
 * - max のみ: HAVING attribute <= max
 */
class ThenHavingBetween extends PromiseQuery
{
    protected BetweenTarget $target;

    /**
     * @param  string  $attribute  HAVING 対象（列名またはエイリアス）
     * @param  string|null  $inputTarget  リクエストキー（未指定は $attribute）
     * @param  string  $minPostfix  リクエストキーの最小側サフィックス（既定: _min）
     * @param  string  $maxPostfix  リクエストキーの最大側サフィックス（既定: _max）
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        string $minPostfix = '_min',
        string $maxPostfix = '_max',
    ) {
        $target = $inputTarget ?? $this->attribute;
        $this->target = new BetweenTarget(new ScalarTarget($target, $maxPostfix), new ScalarTarget($target, $minPostfix));
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        // HAVING 句はテーブル接頭辞は不要（エイリアスを想定）
        $attribute = $this->attribute;

        if ($this->target->is($request)) {
            [$min, $max] = $this->target->value($request);

            return $builder->havingBetween($attribute, [$min, $max]);
        }

        if ($this->target->min()->is($request)) {
            return $builder->having($attribute, '>=', $this->target->min()->value($request));
        }

        if ($this->target->max()->is($request)) {
            return $builder->having($attribute, '<=', $this->target->max()->value($request));
        }

        return $builder;
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
