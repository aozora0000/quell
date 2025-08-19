<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Formatter;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Formatter\LikeFormatter;

/**
 * 単一キーワードを複数カラムに OR-LIKE で適用する簡易エイリアス。
 * 内部的には ThenWheresOrLike を利用します。
 */
class ThenWhereLikeMultiColumns extends PromiseQuery
{
    /**
     * @var ThenWheresOrLike 内部委譲用
     */
    private ThenWheresOrLike $delegate;

    /**
     * @param string[] $attributes 対象カラム配列
     * @param string $target 入力キー
     * @param string|null $table テーブル指定（任意）
     * @param Formatter $formatter LIKEフォーマッタ（既定: 部分一致）
     */
    public function __construct(
        array $attributes,
        string $target,
        ?string $table = null,
        Formatter $formatter = LikeFormatter::PARTIAL_MATCH,
    ) {
        $this->delegate = new ThenWheresOrLike($attributes, $target, $table, $formatter);
    }

    public function match(Request $request): bool
    {
        return $this->delegate->match($request);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        return $this->delegate->resolve($request, $builder);
    }
}
