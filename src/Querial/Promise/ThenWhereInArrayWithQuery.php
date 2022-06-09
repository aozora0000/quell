<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-26
 * Time: 06:57
 */
namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;
use Querial\Contracts\Support\CreateAttributeFromTable;
use Querial\Target\ArrayOrScalarTarget;

class ThenWhereInArrayWithQuery implements PromiseInterface
{
    use CreateAttributeFromTable;
    /**
     * @var string
     */
    protected string $attribute;

    /**
     * @var ArrayOrScalarTarget
     */
    protected ArrayOrScalarTarget $target;

    /**
     * FactoryInterface constructor.
     *
     * @param string      $attribute
     * @param string|null $inputTarget
     */
    public function __construct(string $attribute, ?string $inputTarget = null)
    {
        $this->attribute   = $attribute;
        $this->target      = new ArrayOrScalarTarget($inputTarget ?: $attribute);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function resolveIf(Request $request): bool
    {
        return $this->target->is($request);
    }

    /**
     * @param Request $request
     * @param Builder $builder
     *
     * @return Builder
     */
    public function resolve(Request $request, Builder $builder): Builder
    {
        if (!$this->resolveIf($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);

        return $builder->whereIn($attribute, $this->target->of($request));
    }
}
