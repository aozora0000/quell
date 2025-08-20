<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * リクエストで指定されたカラムと方向に基づいて ORDER BY を適用する Promise。
 * - 許可カラムのホワイトリストを超える指定はデフォルトにフォールバックします。
 * - 方向は asc/desc のみ許可し、それ以外はデフォルトにフォールバックします。
 */
class ThenOrderBy extends PromiseQuery
{
    /**
     * @param  string[]  $allowedColumns  許可するカラム名（テーブル接頭辞なし）。
     * @param  string  $columnKey  カラムを受け取る入力キー（既定: column）
     * @param  string  $dirKey  方向を受け取る入力キー（既定: dir）
     * @param  array{0:string,1:string}  $default  [column, dir] のデフォルト
     * @param  string|null  $table  テーブル指定（任意）。未指定時はモデルのテーブル名。
     */
    public function __construct(
        private readonly array $allowedColumns,
        private readonly string $columnKey = 'column',
        private readonly string $dirKey = 'dir',
        private readonly array $default = ['id', 'asc'],
        ?string $table = null,
    ) {
        $this->table = $table;
        $this->columnTarget = new ScalarTarget($this->columnKey);
        $this->dirTarget = new ScalarTarget($this->dirKey);
    }

    private ScalarTarget $columnTarget;

    private ScalarTarget $dirTarget;

    public function match(Request $request): bool
    {
        // 常に ORDER を適用したいが、リクエスト未指定時はデフォルトにフォールバック
        return true;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        [$column, $dir] = $this->pickColumnAndDirection($request, $builder);

        return $builder->orderBy($column, $dir);
    }

    /**
     * @return array{0:string,1:'asc'|'desc'} [qualifiedColumn, direction]
     */
    private function pickColumnAndDirection(Request $request, EloquentBuilder $builder): array
    {
        $dir = $this->dirTarget->is($request) ? strtolower($this->dirTarget->value($request)) : $this->default[1];
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : (strtolower($this->default[1]) === 'desc' ? 'desc' : 'asc');

        $column = $this->columnTarget->is($request) ? $this->columnTarget->value($request) : $this->default[0];
        $column = in_array($column, $this->allowedColumns, true) ? $column : $this->default[0];

        $qualified = $this->createAttributeFromTable($builder, $column);

        return [$qualified, $dir];
    }
}
