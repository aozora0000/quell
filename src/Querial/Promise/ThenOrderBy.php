<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Helper\Str;
use Querial\Target\ScalarTarget;
use Querial\Helper\Order;

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
        $dirRaw = $this->dirTarget->is($request) ? $this->dirTarget->value($request) : null;
        $colRaw = $this->columnTarget->is($request) ? $this->columnTarget->value($request) : null;
        [$column, $dir] = Str::pickColumnAndDirection($colRaw, $dirRaw, $this->allowedColumns, $this->default);
        $qualified = $this->createAttributeFromTable($builder, $column);

        return $builder->orderBy($qualified, $dir);
    }

}
