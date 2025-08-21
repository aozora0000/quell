<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Formatter;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Formatter\LikeFormatter;

/**
 * 単一キーワードを複数カラムに OR-LIKE（前方一致）で適用するPromise。
 * 内部的には ThenWheresOrLike + LikeFormatter::FORWARD_MATCH を利用します。
 */
class ThenWhereStartsWithMultiColumns extends PromiseQuery
{
    /** @var ThenWheresOrLike 委譲実装 */
    private ThenWheresOrLike $delegate;

    /**
     * @param  string[]  $attributes  対象カラム配列
     * @param  string  $target  入力キー
     * @param  string|null  $table  テーブル指定（任意）
     * @param  Formatter|null $formatter  LIKEフォーマッタ（省略時は前方一致）
     */
    public function __construct(
        array $attributes,
        string $target,
        ?string $table = null,
        ?Formatter $formatter = null,
    ) {
        $this->delegate = new ThenWheresOrLike($attributes, $target, $table, $formatter ?? LikeFormatter::FORWARD_MATCH);
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
