<?php declare(strict_types=1);
namespace Querial\Contracts\Support;


use Querial\Contracts\PromiseInterface;
use Querial\Exceptions\UnsupportedClassTypeException;

abstract class PromiseAggregateImpl implements PromiseInterface
{
    /**
     * @var PromiseInterface[]
     */
    protected array $promises = [];

    /**
     * ThenOrPromisesAggregator constructor.
     *
     * @param PromiseInterface[] $promises
     */
    final public function __construct(array $promises)
    {
        foreach ($promises as $promise) {
            if (!$promise instanceof PromiseInterface) {
                throw new UnsupportedClassTypeException('PromiseInterfaceに属していないクラスは使用出来ません');
            }
        }
        $this->promises = $promises;
    }
}
