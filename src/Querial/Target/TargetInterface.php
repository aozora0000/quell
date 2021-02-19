<?php declare(strict_types=1);
namespace Querial\Target;

use Illuminate\Http\Request;

interface TargetInterface
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isTarget(Request $request): bool;

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function getTarget(Request $request);
}
