<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-16
 * Time: 13:02
 */

namespace Querial\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

interface PipelineInterface
{
    /**
     * @param PromiseInterface $promise
     * @return static
     */
    public function then(PromiseInterface $promise): self;

    /**
     * @param Closure $callback
     * @return static
     */
    public function onFailed(Closure $callback): self;

    /**
     * @return bool
     */
    public function hasFailed(): bool;

    /**
     * @param Closure $callback
     * @return static
     */
    public function onFinally(Closure $callback): self;

    /**
     * @return bool
     */
    public function hasFinally(): bool;

    /**
     * @param Builder $builder
     * @return Builder
     * @throws Throwable
     */
    public function build(Builder $builder): Builder;
}
