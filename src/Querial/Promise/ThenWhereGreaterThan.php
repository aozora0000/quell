<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;

class ThenWhereGreaterThan extends ThenWhereEqual
{
    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return $builder->where($attribute, '>', $this->target->value($request));
    }
}
