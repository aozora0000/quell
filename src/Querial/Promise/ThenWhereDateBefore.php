<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\DatetimeTarget;

/**
 * 指定日付以前（当日を含む）で絞り込むPromise。
 *
 * 例: to=2025-12-31 → WHERE users.created_at <= '2025-12-31 23:59:59'
 */
class ThenWhereDateBefore extends PromiseQuery
{
    protected DatetimeTarget $target;

    /**
     * @param  string  $attribute  対象カラム
     * @param  string|null  $inputTarget  リクエストのキー（未指定時は$attribute）
     * @param  string  $format  受け取るフォーマット（既定: Y-m-d）
     * @param  string|null  $table  テーブル名
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        string $format = 'Y-m-d',
        ?string $table = null,
    ) {
        $this->target = new DatetimeTarget($format, $inputTarget ?? $this->attribute);
        $this->table = $table;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $day = $this->target->value($request);
        $end = $day->copy()->endOfDay();

        return $builder->where($attribute, '<=', $end);
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }
}
