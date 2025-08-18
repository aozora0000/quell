<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * REGEXPによる正規表現検索を適用するPromise。
 */
class ThenWhereRegex extends PromiseQuery
{
    protected ScalarTarget $target;

    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        ?string $table = null,
    ) {
        $this->target = new ScalarTarget($inputTarget ?? $this->attribute);
        $this->table = $table;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return $builder->where($attribute, 'REGEXP', $this->target->value($request));
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }
}
