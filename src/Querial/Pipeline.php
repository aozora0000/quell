<?php declare(strict_types=1);
namespace Querial;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\PipelineInterface;
use Querial\Contracts\PromiseInterface;
use Querial\Contracts\Support\ResolvedFilter;
use Throwable;

class Pipeline implements PipelineInterface
{
    use ResolvedFilter;

    /**
     * @var Request
     */
    private Request $request;

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
            foreach ($promises as $promise) {
                $builder = $promise->resolve($this->request, $builder);
            }
        } catch (Throwable $exception) {
            if ($this->failed === null) {
                throw $exception;
            }
            $builder = call_user_func($this->failed, $this->request, $builder, $exception);
        }

        if ($this->finally !== null) {
            $builder = call_user_func($this->finally, $this->request, $builder);
        }

        return $builder;
    }
}
