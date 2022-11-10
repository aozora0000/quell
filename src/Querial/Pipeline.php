<?php declare(strict_types=1);
namespace Querial;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\PipelineInterface;
use Querial\Contracts\PromiseInterface;
use Throwable;

class Pipeline implements PipelineInterface
{

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var bool
     */
    private bool $is_default = true;

    /**
     * @var array<PromiseInterface>
     */
    private array $promises = [];

    /**
     * @var Closure|null
     */
    private ?Closure $failed = null;

    /**
     * @var Closure|null
     */
    private ?Closure $finally = null;

    /**
     * @var Closure|null
     */
    private ?Closure $default = null;

    /**
     * IlluminateRequestCriteria constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param PromiseInterface $promise
     *
     * @return static
     */
    public function then(PromiseInterface $promise): self
    {
        $this->promises[] = $promise;

        return $this;
    }

    /**
     * @param Closure $callback
     *
     * @return static
     */
    public function onFailed(Closure $callback): self
    {
        $this->failed = $callback;

        return $this;
    }

    /**
     * @param Closure $callback
     *
     * @return static
     */
    public function onFinally(Closure $callback): self
    {
        $this->finally = $callback;
        return $this;
    }

    public function onDefault(Closure $closure): self
    {
        $this->default = $closure;
        return $this;
    }

    protected function resolvedFilter(array $promises, Request $request): array
    {
        return array_filter($promises, static function (PromiseInterface $promise) use ($request) {
            return $promise->resolveIf($request);
        });
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     *
     * @throws Throwable
     */
    public function build(Builder $builder): Builder
    {
        try {
            $promises = $this->resolvedFilter($this->promises, $this->request);
            if(count($promises) !== 0) {
                $this->is_default = false;
            }
            foreach ($promises as $promise) {
                $builder = $promise->resolve($this->request, $builder);
            }
        } catch (Throwable $exception) {
            if (!$this->hasFailed()) {
                throw $exception;
            }
            $this->is_default = false;
            $builder = call_user_func($this->failed, $this->request, $builder, $exception);
        }

        if ($this->hasFinally()) {
            $this->is_default = false;
            $builder = call_user_func($this->finally, $this->request, $builder);
        }

        if($this->is_default && $this->hasDefault()) {
            $builder = call_user_func($this->default, $builder);
        }

        return $builder;
    }

    public function hasFailed(): bool
    {
        return $this->failed !== null;
    }

    public function hasFinally(): bool
    {
        return $this->finally !== null;
    }

    public function hasDefault(): bool
    {
        return $this->default !== null;
    }
}
