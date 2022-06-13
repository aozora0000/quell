<?php declare(strict_types=1);
namespace Querial\Contracts;

use Illuminate\Http\Request;

interface TargetInterface
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function is(Request $request): bool;

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function of(Request $request);
}
