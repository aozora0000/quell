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
use Querial\Target\ScalarTarget;

class ThenOrWhereLikeWithQuery implements PromiseInterface
{
    use CreateAttributeFromTable;

    /**
     * @var string
     */
    protected string $attribute;

    /**
     * @var ScalarTarget
     */
    protected ScalarTarget $target;
    /**
     * @var string
     */
    protected string $format;

    /**
     * FactoryInterface constructor.
     * @param string      $attribute
     * @param string|null $inputTarget
     * @param string      $format
     */
    public function __construct(string $attribute, ?string $inputTarget = null, string $format = '%%%s%%')
    {
        $this->attribute = $attribute;
        $this->target = new ScalarTarget($inputTarget ?? $attribute);
        $this->format = $format;
    }

    /**
     * @param Request $request
     * @param Builder $builder
     * @return Builder
     */
    public function resolve(Request $request, Builder $builder): Builder
    {
        if (!$this->resolveIf($request)) {
            return $builder;
        }
        $attribute = $this->createAttributeFromTable($builder, $this->attribute);
        $value = addcslashes($this->target->of($request), '%_\\');

        return $builder->orWhere($attribute, 'LIKE', sprintf($this->format, $value));
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
