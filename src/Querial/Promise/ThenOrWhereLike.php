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
use Querial\Contracts\Formatter;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Formatter\LikeFormatter;
use Querial\Target\ScalarTarget;

class ThenOrWhereLike extends PromiseQuery
{
    protected ScalarTarget $target;

    protected LikeFormatter $formatter;

    /**
     * FactoryInterface constructor.
     */
    public function __construct(
        protected string $attribute,
        ?string $inputTarget = null,
        ?string $table = null,
        Formatter $format = LikeFormatter::PARTIAL_MATCH
    ) {
        $this->target = new ScalarTarget($inputTarget ?? $attribute);
        $this->formatter = $format;
        $this->table = $table;
    }

    public function resolve(Request $request, Builder $builder): Builder
    {
        if (! $this->match($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $value = addcslashes($this->target->value($request), '%_\\');

        return $builder->orWhere($attribute, 'LIKE', $this->formatter->format($value));
    }

    public function match(Request $request): bool
    {
        return $this->target->is($request);
    }
}
