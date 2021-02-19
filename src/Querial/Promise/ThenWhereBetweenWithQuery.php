<?php declare(strict_types = 1);
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
use Querial\Target\BetweenTarget;
use Querial\Target\ScalarTarget;
use Querial\Promises\CreateAttributeFromTable;

class ThenWhereBetweenWithQuery implements PromiseInterface
{
    use CreateAttributeFromTable;

    /**
     * @var string
     */
    protected string $attribute;
    /**
     * @var BetweenTarget
     */
    protected BetweenTarget $target;

    /**
     * FactoryInterface constructor.
     *
     * @param string      $attribute
     * @param string|null $inputTarget
     * @param string      $minPostfix
     * @param string      $maxPostfix
     */
    public function __construct(string $attribute, ?string $inputTarget = null, string $minPostfix = '_min', string $maxPostfix = '_max')
    {
        $this->attribute     = $attribute;
        $target              = $inputTarget ?? $attribute;
        $this->target        = new BetweenTarget(new ScalarTarget($target, $minPostfix), new ScalarTarget($target, $maxPostfix));
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

        if ($this->target->isTarget($request)) {
            return $builder->whereBetween($attribute, $this->target->getTarget($request));
        }
        if ($this->target->max()->isTarget($request)) {
            $builder->where($attribute, '<=', $this->target->max()->getTarget($request));
        }
        if ($this->target->min()->isTarget($request)) {
            $builder->where($attribute, '>=', $this->target->min()->getTarget($request));
        }

        return $builder;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function resolveIf(Request $request): bool
    {
        return
            $this->target->isTarget($request) ||
            $this->target->max()->isTarget($request) ||
            $this->target->min()->isTarget($request);
    }
}
