<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: aozora0000
 * Date: 2020-06-16
 * Time: 13:02
 */

namespace Querial\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Throwable;

interface PipelineInterface
{
    /**
     * @return static
     */
    public function then(PromiseInterface $promise): self;

    /**
     * @return static
     */
    public function onFailed(Closure $callback): self;

    public function hasFailedClosure(): bool;

    /**
     * @return static
     */
    public function onFinally(Closure $callback): self;

    public function hasFinallyClosure(): bool;

    /**
     * @throws Throwable
     */
    public function build(EloquentBuilder $builder): EloquentBuilder;
}
