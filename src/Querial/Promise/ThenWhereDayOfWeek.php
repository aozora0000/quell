<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * 曜日での絞り込みを行うPromise（MySQL: DAYOFWEEK）。
 *
 * - 入力は 1-7（1=日曜〜7=土曜）を基本とし、0-6 の場合は 0=日曜 として 1-7 へ正規化します。
 */
class ThenWhereDayOfWeek extends PromiseQuery
{
    protected ScalarTarget $target;

    /**
     * @param  string  $attribute  日付/日時カラム
     * @param  string|null  $inputTarget  リクエストのキー（未指定時は dow ）
     * @param  string|null  $table  テーブル名（未指定時はモデルのテーブル）
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = 'dow',
        ?string $table = null,
    ) {
        $this->target = new ScalarTarget($inputTarget ?? 'dow');
        $this->table = $table;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $target = (int) $this->target->value($request);
        $value = match (true) {
            $target >= 1 && $target <= 7 => $target,
            $target >= 0 && $target <= 6 => $target + 1,
            default => 1,
        };
        // DAYOFWEEK(`table`.`column`) = N をそのまま埋め込む（toRawSqlの比較を安定させる）
        [$tbl, $col] = explode('.', $attribute, 2);

        return $builder->whereRaw(sprintf('DAYOFWEEK(`%s`.`%s`) = %d', $tbl, $col, $value));
    }

    public function match(Request $request): bool
    {
        if (! $this->target->is($request)) {
            return false;
        }
        $v = (int) $this->target->value($request);

        return ($v >= 0 && $v <= 6) || ($v >= 1 && $v <= 7);
    }
}
