<?php declare(strict_types=1);
namespace Querial\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;

interface PromiseInterface
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function resolveIf(Request $request): bool;

    /**
     * @param Request         $request
     * @param EloquentBuilder $builder
     * @return EloquentBuilder
     */
    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder;
}
