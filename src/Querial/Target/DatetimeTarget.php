<?php

declare(strict_types=1);

namespace Querial\Target;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;
use Querial\Contracts\TargetInterface;
use ReturnTypeWillChange;

class DatetimeTarget implements TargetInterface
{
    protected string $target;

    public function __construct(protected string $format, string $target, string $postfix = '')
    {
        $this->target = $target.$postfix;
    }

    public function is(Request $request): bool
    {
        if (! $request->filled($this->target)) {
            return false;
        }

        if (! is_scalar($request->input($this->target))) {
            return false;
        }

        try {
            return $this->value($request) instanceof Carbon;
        } catch (InvalidFormatException) {
            return false;
        }
    }

    #[ReturnTypeWillChange]
    public function value(Request $request): Carbon|false
    {
        return Carbon::createFromFormat($this->format, $request->str($this->target, '')->value());
    }
}
