<?php
declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Helper\Str;
use Querial\Target\ScalarTarget;

/**
 * "1-3,5,8-10" のような数値レンジ+単一値の混在指定を OR 条件に展開する Promise。
 * - レンジは start-end 形式（区切りは可変）。
 * - 単一値は数値とみなして '=' 比較。
 */
class ThenWhereInNumericRangeList extends PromiseQuery
{
    private ScalarTarget $target;

    /**
     * @param string $attribute 対象カラム
     * @param string|null $inputTarget 入力キー（省略時は $attribute）
     * @param string $delimiter 要素区切り（既定: ','）
     * @param string $rangeSeparator レンジ区切り（既定: '-'）
     * @param string|null $table テーブル名（任意）
     */
    public function __construct(
        private readonly string $attribute,
        ?string $inputTarget = null,
        private readonly string $delimiter = ',',
        private readonly string $rangeSeparator = '-',
        ?string $table = null,
    ) {
        $key = ($inputTarget !== null && $inputTarget !== '' && $inputTarget !== '0') ? $inputTarget : $attribute;
        $this->target = new ScalarTarget($key);
        $this->table = $table;
    }

    public function match(Request $request): bool
    {
        if (! $this->target->is($request)) {
            return false;
        }
        $tokens = Str::parseNumericRangeTokens((string) $this->target->value($request), $this->delimiter, $this->rangeSeparator);

        return $tokens !== [];
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $tokens = Str::parseNumericRangeTokens((string) $this->target->value($request), $this->delimiter, $this->rangeSeparator);

        return $builder->where(function (EloquentBuilder $q) use ($tokens, $attribute): void {
            foreach ($tokens as $t) {
                if ($t['type'] === 'range') {
                    $min = (string) $t['min'];
                    $max = (string) $t['max'];
                    $q->orWhereBetween($attribute, [$min, $max]);
                } else { // value
                    $q->orWhere($attribute, '=', (string) $t['value']);
                }
            }
        });
    }
}
