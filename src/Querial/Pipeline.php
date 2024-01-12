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
    private Request $request;

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
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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

    protected function resolvedFilter(array $promises, Request $request): array
    {
        return array_filter($promises, static function (PromiseInterface $promise) use ($request) {
            return $promise->resolveIf($request);
        });
    }

    /**
     * @throws Throwable
     */
    public function build(EloquentBuilder $builder): EloquentBuilder
    {
        try {
            $promises = $this->resolvedFilter($this->promises, $this->request);
            if (count($promises) !== 0) {
                $this->is_default = false;
            }
            foreach ($promises as $promise) {
                $builder = $promise->resolve($this->request, $builder);
            }
        } catch (Throwable $exception) {
            if (! $this->hasFailedClosure()) {
                throw $exception;
            }
            $this->is_default = false;
            call_user_func($this->onFailedClosure, $this->request, $builder, $exception);
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
        return $this->onFailedClosure !== null;
    }

    public function hasFinallyClosure(): bool
    {
        return $this->onFinallyClosure !== null;
    }

    public function hasDefaultClosure(): bool
    {
        return $this->onDefaultClosure !== null;
    }
}
