<?php

declare(strict_types=1);

namespace Querial\Target;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;
use Querial\Contracts\TargetInterface;

class DatetimeTarget implements TargetInterface
{
    protected string $format;

    protected string $target;

    public function __construct(string $format, string $target, string $postfix = '')
    {
        $this->target = $target.$postfix;
        $this->format = $format;
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
        } catch (InvalidFormatException $exception) {
            return false;
        }
    }

    #[\ReturnTypeWillChange]
    public function value(Request $request): Carbon|false
    {
        return Carbon::createFromFormat($this->format, $request->str($this->target, '')->value());
    }
}
