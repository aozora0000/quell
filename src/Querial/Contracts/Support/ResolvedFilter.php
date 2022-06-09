<?php declare(strict_types=1);
namespace Querial\Contracts\Support;

use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;

trait ResolvedFilter
{
    public function resolvedFilter(array $promises, Request $request)
    {
        return array_filter($promises, static function (PromiseInterface $promise) use ($request) {
            return $promise->resolveIf($request);
        });
    }
}
