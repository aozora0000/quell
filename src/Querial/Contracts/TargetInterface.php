<?php

declare(strict_types=1);

namespace Querial\Contracts;

use Illuminate\Http\Request;

interface TargetInterface
{
    public function is(Request $request): bool;

    public function value(Request $request);
}
