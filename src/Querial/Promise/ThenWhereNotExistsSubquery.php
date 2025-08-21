<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\AggregatePromiseQuery;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * 指定されたテーブルに対する NOT EXISTS サブクエリで絞り込む Promise。
 *
 * 例:
 * - new ThenWhereNotExistsSubquery('items', 'user_id', 'id', 'not_exists', $aggregator)
 *   → WHERE NOT EXISTS (SELECT * FROM items WHERE users.id = items.user_id AND ...)
 */
class ThenWhereNotExistsSubquery extends PromiseQuery
{
    private ScalarTarget $flagTarget;

    /**
     * @param string $subTable サブクエリで参照するテーブル名（例: items）
     * @param string $foreignKey サブテーブル側の外部キー（例: user_id）
     * @param string $localKey 親テーブル側のローカルキー（例: id）
     * @param string $inputTarget リクエストのフラグキー（既定: not_exists）
     * @param AggregatePromiseQuery|null $aggregatePromiseQuery サブクエリに適用する条件群（任意）
     */
    public function __construct(
        private readonly string $subTable,
        private readonly string $foreignKey,
        private readonly string $localKey,
        string $inputTarget = 'not_exists',
        private readonly ?AggregatePromiseQuery $aggregatePromiseQuery = null,
    ) {
        $this->flagTarget = new ScalarTarget($inputTarget);
    }

    public function match(Request $request): bool
    {
        // フラグが与えられたら適用（aggregator の一致は必須としない）
        return $this->flagTarget->is($request);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $parentTable = $builder->getModel()->getTable();
        $localQualified = sprintf('%s.%s', $parentTable, $this->localKey);
        $foreignQualified = sprintf('%s.%s', $this->subTable, $this->foreignKey);

        return $builder->whereNotExists(function (QueryBuilder $q) use ($request, $localQualified, $foreignQualified): void {
            // FROM サブテーブル + 親=子 のカラム結合
            $q->from($this->subTable)
                ->select('*')
                ->whereColumn($localQualified, $foreignQualified);

            // 追加条件をサブクエリ側へ適用
            if ($this->aggregatePromiseQuery !== null) {
                // EloquentBuilder 互換のため、クエリビルダをラップ
                $eloquent = new class($q) extends EloquentBuilder {
                    public function __construct(QueryBuilder $query)
                    {
                        // ダミーモデルなしで QueryBuilder をラップ
                        $this->query = $query;
                    }
                };
                $this->aggregatePromiseQuery->resolve($request, $eloquent);
            }
        });
    }
}
