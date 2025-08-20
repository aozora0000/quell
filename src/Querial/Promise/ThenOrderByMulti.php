<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * 複数キー優先で ORDER BY を適用する Promise。
 * 入力例: sort="-created_at,name" → ORDER BY created_at desc, name asc
 */
class ThenOrderByMulti extends PromiseQuery
{
    /**
     * @param  array<string,string>  $allowedMap  入力トークン→実カラム名（テーブル接頭辞なし推奨）
     * @param  string  $inputKey  入力キー名（既定: sort）
     * @param  string[]  $defaultTokens  デフォルトトークン列（例: ['-created_at','id']）
     * @param  string|null  $table  テーブル指定（任意）
     */
    public function __construct(
        private readonly array $allowedMap,
        private readonly string $inputKey = 'sort',
        private readonly array $defaultTokens = [],
        ?string $table = null,
    ) {
        $this->table = $table;
        $this->target = new ScalarTarget($this->inputKey);
    }

    private ScalarTarget $target;

    public function match(Request $request): bool
    {
        // リクエスト未指定でもデフォルトでソートできるよう常にtrue
        return true;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        $tokens = $this->parseTokens($request);
        foreach ($tokens as [$column, $dir]) {
            // マッピングの値が「テーブル.カラム」でない場合は createAttributeFromTable で補う
            $qualified = str_contains($column, '.') ? $column : $this->createAttributeFromTable($builder, $column);
            $builder->orderBy($qualified, $dir);
        }

        return $builder;
    }

    /**
     * @return array<array{0:string,1:'asc'|'desc'}>
     */
    private function parseTokens(Request $request): array
    {
        $raw = $this->target->is($request) ? $this->target->value($request) : implode(',', $this->defaultTokens);
        $parts = array_values(array_filter(array_map('trim', explode(',', $raw)), fn ($v) => $v !== ''));

        $result = [];
        foreach ($parts as $token) {
            $dir = str_starts_with($token, '-') ? 'desc' : 'asc';
            $key = ltrim($token, '+-');
            if (! array_key_exists($key, $this->allowedMap)) {
                continue; // 不正トークンは無視
            }
            $column = $this->allowedMap[$key];
            $result[] = [$column, $dir];
        }

        // すべて不正だった場合、デフォルトへ
        if ($result === [] && $this->defaultTokens !== []) {
            foreach ($this->defaultTokens as $token) {
                $dir = str_starts_with($token, '-') ? 'desc' : 'asc';
                $key = ltrim($token, '+-');
                if (! array_key_exists($key, $this->allowedMap)) {
                    continue;
                }
                $result[] = [$this->allowedMap[$key], $dir];
            }
        }

        return $result;
    }
}
