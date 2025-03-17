<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 06:57
 */

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Contracts\TargetInterface;
use Querial\Target\ScalarTarget;

class ThenWhereNotEqual extends PromiseQuery
{
    protected TargetInterface $target;

    /**
     * FactoryInterface constructor.
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        ?string $table = null)
    {
        $this->target = new ScalarTarget($inputTarget ?: $attribute);
        $this->table = $table;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return $builder->where($attribute, '<>', $this->target->value($request));
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }
}
