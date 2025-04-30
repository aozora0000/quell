<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Contracts\TargetInterface;
use Querial\Target\ScalarTarget;

class ThenWhereFullText extends PromiseQuery
{
    protected TargetInterface $target;

    /**
     * FactoryInterface constructor.
     */
    public function __construct(protected string $attribute,
        ?string $inputTarget = null,
        ?string $table = null)
    {
        $this->target = new ScalarTarget($inputTarget !== null && $inputTarget !== '' && $inputTarget !== '0' ? $inputTarget : $attribute);
        $this->table = $table;
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        return $builder->whereFullText($this->attribute, $this->target->value($request));
    }
}
