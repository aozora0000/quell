<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * 指定されたパラメータが存在する場合に、カラムがNULLではないレコードに絞り込むPromise。
 */
class ThenWhereNotNull extends PromiseQuery
{
    protected ScalarTarget $target;

    /**
     * @param  string  $attribute  絞り込み対象のカラム
     * @param  string|null  $inputTarget  リクエストのキー(未指定時は$attribute)
     * @param  string|null  $table  テーブル指定(未指定時はモデルテーブル)
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        ?string $table = null
    ) {
        $this->target = new ScalarTarget($inputTarget ?? $this->attribute);
        $this->table = $table;
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return $builder->whereNotNull($attribute);
    }

    public function match(Request $request): bool
    {
        // filledかつスカラーの場合にのみ適用
        return $this->target->is($request);
    }
}
