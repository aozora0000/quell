<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * REGEXPの否定（NOT REGEXP）による正規表現検索を適用するPromise。
 */
class ThenWhereRegexNot extends PromiseQuery
{
    protected ScalarTarget $target;

    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        ?string $table = null,
    ) {
        // 入力の取得対象キー。未指定なら属性名を使う
        $this->target = new ScalarTarget($inputTarget ?? $this->attribute);
        $this->table = $table;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return $builder->where($attribute, 'NOT REGEXP', $this->target->value($request));
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }
}
