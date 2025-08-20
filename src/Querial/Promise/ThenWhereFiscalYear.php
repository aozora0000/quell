<?php

declare(strict_types=1);

namespace Querial\Promise;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * 日本の会計年度など、任意の開始月での「年度」指定により期間絞り込みを行うPromise。
 * 例: 開始月=4(4月)・年度=2024 → 2024-04-01 00:00:00 〜 2025-03-31 23:59:59
 */
class ThenWhereFiscalYear extends PromiseQuery
{
    protected ScalarTarget $yearTarget;

    public function __construct(
        protected string $attribute,
        protected int $startMonth = 4,
        protected ?string $inputTarget = 'year',
        ?string $table = null,
    ) {
        $this->yearTarget = new ScalarTarget($this->inputTarget ?? 'year');
        $this->table = $table;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $year = (int) $this->yearTarget->value($request);
        $startMonth = max(1, min(12, $this->startMonth));

        // タイムゾーンはUTC固定（テストのSQL比較に影響しないため）。実運用ではアプリTZを検討。
        $tz = new DateTimeZone('UTC');

        // 例: startMonth=4 / year=2024 → 2024-04-01 00:00:00
        $start = (new DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $year, $startMonth), $tz));

        // 期末は翌年の startMonth-1 の月末 23:59:59
        $endYear = $startMonth === 1 ? $year : $year + 1; // 1月開始は同年末
        $endMonth = $startMonth === 1 ? 12 : $startMonth - 1;

        // 月末日の算出（次月1日の前秒を使う）
        $end = (new DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $endYear, $endMonth), $tz))
            ->modify('+1 month')
            ->modify('-1 second');

        return $builder->whereBetween($attribute, [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')]);
    }

    public function match(Request $request): bool
    {
        return $this->yearTarget->is($request);
    }
}
