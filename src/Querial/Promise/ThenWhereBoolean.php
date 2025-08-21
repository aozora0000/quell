<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Helper\Str;
use Querial\Target\ScalarTarget;

/**
 * 真偽値（boolean）カラムに対して、指定キーが存在する場合に等価絞り込みを行うPromise。
 *
 * - 許容される真の値: 1, true, on, yes（大文字小文字を無視）
 * - 許容される偽の値: 0, false, off, no（大文字小文字を無視）
 * - 上記以外の値が指定された場合は絞り込みを適用しない。
 */
class ThenWhereBoolean extends PromiseQuery
{
    protected ScalarTarget $target;

    /**
     * @param  string  $attribute  真偽カラム名
     * @param  string|null  $inputTarget  リクエストのキー（未指定時は$attribute）
     * @param  string|null  $table  テーブル名（未指定時はモデルのテーブル）
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        ?string $table = null,
    ) {
        $this->target = new ScalarTarget($inputTarget ?? $this->attribute);
        $this->table = $table;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $value = Str::isTruthy($this->target->value($request));

        return $builder->where($attribute, '=', $value ? 1 : 0);
    }

    public function match(Request $request): bool
    {
        if (! $this->target->is($request)) {
            return false;
        }
        $raw = $this->target->value($request);

        return Str::isTruthy($raw) || Str::isFalsy($raw);
    }

}
