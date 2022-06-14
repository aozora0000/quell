<?php declare(strict_types = 1);
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

class ThenWhereNotEqualWithQuery extends PromiseQuery
{
    /**
     * @var string
     */
    protected string $attribute;

    /**
     * @var TargetInterface
     */
    protected TargetInterface $target;

    /**
     * FactoryInterface constructor.
     * @param string      $attribute
     * @param string|null $inputTarget
     * @param string|null $table
     */
    public function __construct(string $attribute, ?string $inputTarget = null, ?string $table = null)
    {
        $this->attribute = $attribute;
        $this->target = new ScalarTarget($inputTarget ?: $attribute);
        $this->table = $table;
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (!$this->resolveIf($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return $builder->where($attribute, '<>', $this->target->of($request));
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function resolveIf(Request $request): bool
    {
        return $this->target->is($request);
    }
}
