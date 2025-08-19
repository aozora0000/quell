<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\DatetimeTarget;

/**
 * 指定された日付(フォーマット指定可)と同一日の範囲で絞り込むPromise。
 */
class ThenWhereDateEqual extends PromiseQuery
{
    protected DatetimeTarget $target;

    /**
     * @param string $attribute 絞り込み対象のカラム
     * @param string|null $inputTarget リクエストのキー(未指定時は$attribute)
     * @param string $format 受け取る日付フォーマット(既定: Y-m-d)
     * @param string|null $table テーブル指定
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        string $format = 'Y-m-d',
        ?string $table = null
    ) {
        $this->target = new DatetimeTarget($format, $inputTarget ?? $this->attribute);
        $this->table = $table;
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $day = $this->target->value($request);

        // Carbonはミュータブルなため、start/endで別インスタンスを用意する
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        return $builder->whereBetween($attribute, [$start, $end]);
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }
}
