<?php

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ThenWhereNotLike extends ThenWhereLike
{
    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $value = addcslashes($this->target->value($request), '%_\\');

        return $builder->where($attribute, 'not LIKE', $this->formatter->format($value));
    }
}
