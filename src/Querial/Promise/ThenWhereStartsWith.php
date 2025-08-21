<?php

declare(strict_types=1);

namespace Querial\Promise;

use Querial\Formatter\LikeFormatter;

/**
 * 前方一致の LIKE 検索を行う Promise。
 * 例: name=\"abc\" → where name like 'abc%'
 */
class ThenWhereStartsWith extends ThenWhereLike
{
    /**
     * @param string $attribute 対象カラム
     * @param string|null $inputTarget 入力キー（省略時は $attribute）
     * @param string|null $table テーブル名（任意）
     */
    public function __construct(string $attribute, ?string $inputTarget = null, ?string $table = null)
    {
        parent::__construct($attribute, $inputTarget, $table, LikeFormatter::FORWARD_MATCH);
    }
}
