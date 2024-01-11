<?php

declare(strict_types=1);

namespace Querial\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;

interface PromiseInterface
{
    public function resolveIf(Request $request): bool;

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder;
}
