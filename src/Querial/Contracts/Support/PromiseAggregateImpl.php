<?php declare(strict_types=1);
namespace Querial\Contracts\Support;

use Querial\Contracts\PromiseInterface;
use Querial\Exceptions\InvalidClassException;

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
                throw new InvalidClassException('Required PromiseInterface Implement in Class');
            }
        }
        $this->promises = $promises;
    }
}
