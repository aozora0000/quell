<?php

namespace Querial\Contracts;

use Illuminate\Database\Eloquent\Builder;
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
     * @param Request $request
     * @param Builder $builder
     *
     * @return Builder
     */
    public function resolve(Request $request, Builder $builder): Builder;
}