<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 06:57
 */

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ArrayOrScalarTarget;

class ThenWhereInArray extends PromiseQuery
{
    protected string $attribute;

    protected ArrayOrScalarTarget $target;

    /**
     * FactoryInterface constructor.
     */
    public function __construct(string $attribute, ?string $inputTarget = null, ?string $table = null)
    {
        $this->attribute = $attribute;
        $this->target = new ArrayOrScalarTarget($inputTarget ?: $attribute);
        $this->table = $table;
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->match($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return $builder->whereIn($attribute, $this->target->value($request));
    }
}
