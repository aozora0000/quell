<?php

declare(strict_types=1);

namespace Querial;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\PipelineInterface;
use Querial\Contracts\PromiseInterface;
use Throwable;

class Pipeline implements PipelineInterface
{
    private bool $is_default = true;

    /**
     * @var array<PromiseInterface>
     */
    private array $promises = [];

    private ?Closure $onFailedClosure = null;

    private ?Closure $onFinallyClosure = null;

    private ?Closure $onDefaultClosure = null;

    /**
     * IlluminateRequestCriteria constructor.
     */
    public function __construct(private readonly Request $request) {}

    /**
     * @return static
     */
    public function then(PromiseInterface $promise): self
    {
        $this->promises[] = $promise;

        return $this;
    }

    /**
     * @return static
     */
    public function onFailed(Closure|callable $callback): self
    {
        $this->onFailedClosure = $callback;

        return $this;
    }

    /**
     * @return static
     */
    public function onFinally(Closure|callable $callback): self
    {
        $this->onFinallyClosure = $callback;

        return $this;
    }

    public function onDefault(Closure|callable $closure): self
    {
        $this->onDefaultClosure = $closure;

        return $this;
    }

    protected function getMatchedPromises(array $promises, Request $request): array
    {
        return array_filter($promises, static fn (PromiseInterface $promise): bool => $promise->match($request));
    }

    /**
     * @throws Throwable
     */
    public function build(EloquentBuilder $builder): EloquentBuilder
    {
        try {
            $promises = $this->getMatchedPromises($this->promises, $this->request);
            if ($promises !== []) {
                $this->is_default = false;
            }

            foreach ($promises as $promise) {
                $builder = $promise->resolve($this->request, $builder);
            }
        } catch (Throwable $throwable) {
            throw_unless($this->hasFailedClosure(), $throwable);

            $this->is_default = false;
            call_user_func($this->onFailedClosure, $this->request, $builder, $throwable);
        }

        if ($this->hasFinallyClosure()) {
            call_user_func($this->onFinallyClosure, $this->request, $builder);
        }

        if ($this->is_default && $this->hasDefaultClosure()) {
            call_user_func($this->onDefaultClosure, $this->request, $builder);
        }

        return $builder;
    }

    public function hasFailedClosure(): bool
    {
        return $this->onFailedClosure instanceof \Closure;
    }

    public function hasFinallyClosure(): bool
    {
        return $this->onFinallyClosure instanceof \Closure;
    }

    public function hasDefaultClosure(): bool
    {
        return $this->onDefaultClosure instanceof \Closure;
    }
}
