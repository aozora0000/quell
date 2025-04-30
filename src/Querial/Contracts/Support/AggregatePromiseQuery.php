<?php

declare(strict_types=1);

namespace Querial\Contracts\Support;

use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;
use Querial\Exceptions\InvalidClassException;
use Throwable;

abstract class AggregatePromiseQuery implements PromiseInterface
{
    /**
     * @var PromiseInterface[]
     */
    protected array $promises = [];

    /**
     * ThenOrPromisesAggregator constructor.
     *
     * @param  PromiseInterface[]  $promises
     *
     * @throws Throwable
     */
    final public function __construct(array $promises)
    {
        foreach ($promises as $promise) {
            throw_unless($promise instanceof PromiseInterface, new InvalidClassException('Required PromiseInterface Implement in Class'));
        }

        $this->promises = $promises;
    }

    public function match(Request $request): bool
    {
        return $this->getMatchedPromises($this->promises, $request) !== [];
    }

    /**
     * @param  PromiseInterface[]  $promises
     * @return PromiseInterface[]
     */
    protected function getMatchedPromises(array $promises, Request $request): array
    {
        return array_filter($promises, static fn (PromiseInterface $promise): bool => $promise->match($request));
    }
}
