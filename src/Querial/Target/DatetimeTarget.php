<?php declare(strict_types = 1);

namespace Querial\Target;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;

class DatetimeTarget extends ScalarTarget
{
    protected string $format;

    /**
     * @param string $format
     * @param string $target
     * @param string $postfix
     */
    public function __construct(string $format, string $target, string $postfix = '')
    {
        parent::__construct($target, $postfix);
        $this->format = $format;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function is(Request $request): bool
    {
        if (!parent::is($request)) {
            return false;
        }
        $value = parent::of($request);
        try {
            return Carbon::createFromFormat($this->format, $value) instanceof Carbon;
        } catch (InvalidFormatException $exception) {
            return false;
        }
    }

    /**
     * @param Request $request
     * @return Carbon
     */
    public function of(Request $request)
    {
        return Carbon::createFromFormat($this->format, parent::of($request));
    }
}
