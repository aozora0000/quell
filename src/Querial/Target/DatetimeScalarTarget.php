<?php declare(strict_types=1);
namespace Querial\Target;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DatetimeScalarTarget extends ScalarTarget
{

    /**
     * @param Request $request
     *
     * @return Carbon
     */
    public function getTarget(Request $request)
    {
        return Carbon::parse(parent::getTarget($request));
    }
}
