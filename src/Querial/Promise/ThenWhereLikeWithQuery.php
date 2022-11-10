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
use Querial\Contracts\Support\PromiseQuery;
use Querial\Formatter\LikeFormatter;
use Querial\Target\ScalarTarget;

class ThenWhereLikeWithQuery extends PromiseQuery
{
    /**
     * @var string
     */
    protected string $attribute;

    /**
     * @var ScalarTarget
     */
    protected ScalarTarget $target;

    /**
     * @var LikeFormatter
     */
    protected LikeFormatter $formatter;

    /**
     * FactoryInterface constructor.
     * @param string      $attribute
     * @param string|null $inputTarget
     * @param string|null $table
     * @param string      $format
     */
    public function __construct(string $attribute, ?string $inputTarget = null, ?string $table = null, string $format = '%%%s%%')
    {
        $this->attribute = $attribute;
        $this->target = new ScalarTarget($inputTarget ?? $attribute);
        $this->formatter = new LikeFormatter($format);
        $this->table = $table;
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
        $value     = addcslashes($this->target->of($request), '%_\\');

        return $builder->where($attribute, 'LIKE', $this->formatter->format($value));
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
}
