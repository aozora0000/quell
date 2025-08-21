<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Helper\Str;
use Querial\Target\ScalarTarget;

/**
 * 区切り文字で分割した文字列を whereNotIn に変換して適用する Promise。
 * 例: ids="1,2,3" → where id not in (1,2,3)
 *
 * 注意:
 * - トークンは trim され、空要素は除外します。
 * - デフォルトの区切りはカンマ(',')です。
 * - $cast = 'string' | 'int' | 'float' を指定可能（既定: 'string'）。
 */
class ThenWhereNotInSplitArray extends PromiseQuery
{
    private ScalarTarget $target;

    /**
     * @param  string  $attribute  対象カラム名
     * @param  string|null  $inputTarget  入力キー（未指定時は $attribute と同名）
     * @param  string  $delimiter  区切り文字（既定 ","）
     * @param  'string'|'int'|'float'  $cast  各要素のキャスト方法（既定 'string'）
     * @param  string|null  $table  テーブル名（任意）
     */
    public function __construct(
        private readonly string $attribute,
        ?string $inputTarget = null,
        private readonly string $delimiter = ',',
        private readonly string $cast = 'string',
        ?string $table = null,
    ) {
        $key = ($inputTarget !== null && $inputTarget !== '' && $inputTarget !== '0') ? $inputTarget : $attribute;
        $this->target = new ScalarTarget($key);
        $this->table = $table;
    }

    public function match(Request $request): bool
    {
        if (! $this->target->is($request)) {
            return false;
        }
        $values = Str::splitToList((string) $this->target->value($request), $this->delimiter, $this->cast);

        return $values !== [];
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $raw = (string) $this->target->value($request);
        $values = Str::splitToList($raw, $this->delimiter, $this->cast);

        return $builder->whereNotIn($attribute, $values);
    }
}
