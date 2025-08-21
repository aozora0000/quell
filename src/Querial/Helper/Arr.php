<?php

namespace Querial\Helper;

class Arr
{
    /**
     * 値を配列に正規化する。
     * - 配列でなければ単一要素配列にする。
     * - 空文字/NULLを除外し、インデックスを詰める。
     *
     * @return array<int, scalar>
     */
    public static function toList(mixed $raw): array
    {
        $list = is_array($raw) ? array_values($raw) : [$raw];
        $list = array_values(array_filter($list, static function ($v) {
            return $v !== '' && $v !== null;
        }));

        return $list;
    }
}