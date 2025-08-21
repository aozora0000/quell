<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;

/**
 * リクエストの連想配列（例: filters[status][]=...）をマップに従って whereIn を適用する Promise。
 * - 入力の親キー（既定: filters）配下に、入力キー→値配列/スカラがある前提。
 * - マップは「入力キー => カラム名」。カラム名が `table.column` 形式でなければ、モデルのテーブル名で修飾します。
 * - 値がスカラの場合は単一要素配列として扱います。
 * - 空文字/NULL を取り除いた結果が空の場合、そのキーは無視します。
 * - 複数キーが有効な場合は AND で連結されます。
 */
class ThenWhereInRequestArrayMap extends PromiseQuery
{
    /**
     * @param  array<string,string>  $map  入力キー=>カラム名のマップ
     * @param  string  $parentKey  親キー名（例: 'filters'）
     * @param  string|null  $table  テーブル名（省略時はモデル側のテーブル）
     */
    public function __construct(
        private readonly array $map,
        private readonly string $parentKey = 'filters',
        ?string $table = null,
    ) {
        $this->table = $table;
    }

    public function match(Request $request): bool
    {
        $bag = $request->input($this->parentKey);
        if (! is_array($bag)) {
            return false;
        }
        foreach ($this->map as $inputKey => $_) {
            if (! array_key_exists($inputKey, $bag)) {
                continue;
            }
            $values = $this->normalizeValues($bag[$inputKey]);
            if ($values !== []) {
                return true;
            }
        }

        return false;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        $bag = $request->input($this->parentKey);
        if (! is_array($bag)) {
            return $builder;
        }

        foreach ($this->map as $inputKey => $column) {
            if (! array_key_exists($inputKey, $bag)) {
                continue;
            }
            $values = $this->normalizeValues($bag[$inputKey]);
            if ($values === []) {
                continue;
            }

            // カラム名が未修飾ならテーブルで修飾する
            $qualified = str_contains($column, '.') ? $column : $this->createAttributeFromTable($builder, $column);
            $builder->whereIn($qualified, $values);
        }

        return $builder;
    }

    /**
     * 値を配列に正規化し、空文字/NULLを除外してインデックスを詰める。
     *
     * @return array<int, scalar>
     */
    private function normalizeValues(mixed $raw): array
    {
        $list = is_array($raw) ? array_values($raw) : [$raw];
        $list = array_values(array_filter($list, static function ($v) {
            return $v !== '' && $v !== null; // 空要素は除外
        }));

        return $list;
    }
}
