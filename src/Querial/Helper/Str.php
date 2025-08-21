<?php

namespace Querial\Helper;

class Str
{
    /**
     * 真を表す文字列か判定する（大文字小文字無視）
     * 許容: '1','true','on','yes'
     */
    public static function isTruthy($val): bool
    {
        return is_string($val) && in_array(strtolower($val), ['1', 'true', 'on', 'yes'], true);
    }

    /**
     * 偽を表す文字列か判定する（大文字小文字無視）
     * 許容: '0','false','off','no'
     */
    public static function isFalsy($val): bool
    {
        return is_string($val) && in_array(strtolower($val), ['0', 'false', 'off', 'no'], true);
    }

    /**
     * 文字列を区切り文字で分割し、trim・空要素除外・型キャストを行った配列を返す。
     * $cast: 'string' | 'int' | 'float'
     *
     * @return array<int, string|int|float>
     */
    public static function splitToList(string $raw, string $delimiter = ',', string $cast = 'string'): array
    {
        if ($raw === '') {
            return [];
        }

        $parts = array_map(static fn ($v) => trim((string) $v), explode($delimiter, $raw));
        $parts = array_values(array_filter($parts, static fn ($v) => $v !== ''));

        if ($parts === []) {
            return [];
        }

        return array_map(static function (string $v) use ($cast) {
            return match ($cast) {
                'int' => (int) $v,
                'float' => (float) $v,
                default => $v,
            };
        }, $parts);
    }

    /**
     * 数値レンジ/単一値混在の文字列をトークン配列にパースする。
     * 例: "1-3,5,8-10" →
     *   [ ['type'=>'range','min'=>1,'max'=>3], ['type'=>'value','value'=>5], ['type'=>'range','min'=>8,'max'=>10] ]
     *
     * - 要素は trim し、空は除外する。
     * - レンジは start-end 形式。start> end は入れ替える。
     * - 数値以外は無視する。
     *
     * @return array<int, array<string, int|float>>
     */
    public static function parseNumericRangeTokens(string $raw, string $delimiter = ',', string $rangeSeparator = '-'): array
    {
        if ($raw === '') {
            return [];
        }

        $list = array_map(static fn ($v) => trim((string) $v), explode($delimiter, $raw));
        $list = array_values(array_filter($list, static fn ($v) => $v !== ''));

        $tokens = [];
        foreach ($list as $item) {
            $pos = strpos($item, $rangeSeparator);
            if ($pos !== false) {
                [$l, $r] = [trim(substr($item, 0, $pos)), trim(substr($item, $pos + strlen($rangeSeparator)))];
                if ($l === '' || $r === '') {
                    continue; // 片側欠損は無視
                }
                if (! is_numeric($l) || ! is_numeric($r)) {
                    continue; // 非数値は無視
                }
                $min = $l + 0; // 数値化（int/float）
                $max = $r + 0;
                if ($min > $max) {
                    [$min, $max] = [$max, $min]; // 逆転時は入れ替え
                }
                $tokens[] = ['type' => 'range', 'min' => $min, 'max' => $max];

                continue;
            }

            if (! is_numeric($item)) {
                continue; // 非数値は無視
            }
            $tokens[] = ['type' => 'value', 'value' => $item + 0];
        }

        return $tokens;
    }

    /**
     * 複数トークンの文字列から ORDER BY 指定の配列に変換する。
     * 例: "-created_at,name" → [ ['created_at','desc'], ['name','asc'] ]
     *
     * - トークン先頭が '-' なら desc、'+' または接頭辞なしなら asc
     * - 許可マップ($allowedMap)に存在しないキーは無視
     * - 全て無効だった場合、$defaultTokens を同様に展開して返す
     *
     * @param  array<string,string>  $allowedMap  入力トークン→実カラム名
     * @param  string[]  $defaultTokens  デフォルトのトークン列（例: ['-created_at','id']）
     * @return array<int, array{0:string,1:'asc'|'desc'}>
     */
    public static function parseOrderByTokens(string $raw, array $allowedMap, array $defaultTokens = []): array
    {
        $parts = array_values(array_filter(array_map('trim', explode(',', $raw)), static fn ($v) => $v !== ''));

        $result = [];
        foreach ($parts as $token) {
            $dir = str_starts_with($token, '-') ? 'desc' : 'asc';
            $key = ltrim($token, '+-');
            if (! array_key_exists($key, $allowedMap)) {
                continue; // 不正トークンは無視
            }
            $column = $allowedMap[$key];
            $result[] = [$column, $dir];
        }

        if ($result === [] && $defaultTokens !== []) {
            foreach ($defaultTokens as $token) {
                $dir = str_starts_with($token, '-') ? 'desc' : 'asc';
                $key = ltrim($token, '+-');
                if (! array_key_exists($key, $allowedMap)) {
                    continue;
                }
                $result[] = [$allowedMap[$key], $dir];
            }
        }

        return $result;
    }

    /**
     * カラムと方向を許可リストとデフォルトに基づいて正規化する。
     *
     * - 方向は asc/desc のみ許容（大文字小文字無視）
     * - カラムは $allowedColumns に存在するもののみ許容
     * - どちらかが無効/未指定なら $default を採用
     *
     * @param  string[]  $allowedColumns  許可されたカラム名
     * @param  array{0:string,1:string}  $default  [column, dir]
     * @return array{0:string,1:'asc'|'desc'}
     */
    public static function pickColumnAndDirection(?string $column, ?string $dir, array $allowedColumns, array $default): array
    {
        $dirNorm = is_string($dir) ? strtolower($dir) : null;
        $dirNorm = in_array($dirNorm, ['asc', 'desc'], true) ? $dirNorm : (strtolower($default[1]) === 'desc' ? 'desc' : 'asc');

        $colNorm = is_string($column) ? $column : null;
        $colNorm = in_array($colNorm, $allowedColumns, true) ? $colNorm : $default[0];

        return [$colNorm, $dirNorm];
    }

    /**
     * 数値をSQL文字列としてクォートする（単純なシングルクォート）。
     * - 少数は小数点以下末尾の0と不要なドットを除去する。
     */
    public static function quoteNumber(float $number): string
    {
        // 小数点以下は6桁固定で作成後、末尾の0と小数点をトリムして安定化
        $normalized = rtrim(rtrim(sprintf('%.6F', $number), '0'), '.');

        return "'{$normalized}'";
    }
}
