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
use Querial\Contracts\Support\CreateAttributeFromTable;
use Querial\Target\BetweenTarget;
use Querial\Target\ScalarTarget;

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

        if ($this->target->is($request)) {
            return $builder->whereBetween($attribute, $this->target->of($request));
        }
        if ($this->target->max()->is($request)) {
            $builder->where($attribute, '<=', $this->target->max()->of($request));
        }
        if ($this->target->min()->is($request)) {
            $builder->where($attribute, '>=', $this->target->min()->of($request));
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
            $this->target->is($request) ||
            $this->target->max()->is($request) ||
            $this->target->min()->is($request);
    }
}
