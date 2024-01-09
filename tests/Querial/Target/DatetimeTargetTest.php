<?php

namespace Querial\Target;

use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class DatetimeTargetTest extends TestCase
{
    /**
     * @return array<array{bool,string,array<string, mixed>}>
     */
    public static function dataProvider(): array
    {
        return [
            [
                true,
                'Y-m-d H:i:s',
                [
                    'period' => '2020-01-01 00:00:00',
                ],
            ], [
                true,
                'Y-m-d',
                [
                    'period' => '2020-01-01',
                ],
            ], [
                false,
                'Y-m-d H:i:s',
                [
                    'period' => '2020-01-01',
                ],
            ], [
                true,
                'Y/m/d',
                [
                    'period' => '2020/01/01',
                ],
            ], [
                false,
                'Y-m-d',
                [
                    'period' => '2020/01/01',
                ],
            ], [
                true,
                'U',
                [
                    'period' => '1577836800',
                ],
            ], [
                // TODO: 12月扱いになるが許容すべきなのか？Datetimeもstrtotimeに準拠する？
                true,
                'Y-m-d H:i:s',
                [
                    'period' => '2020-00-01 00:00:00',
                ],
            ], [
                true,
                'H:i:s',
                [
                    'period' => '00:00:00',
                ],
            ], [
                false,
                'Y-m-d H:i:s',
                [
                    'period' => 'testetst',
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param  array<string, mixed>  $data
     */
    public function testIs(bool $expect, string $format, array $data): void
    {
        $target = new DatetimeTarget($format, 'period');
        $request = Request::create('/', 'GET', $data);
        static::assertEquals($expect, $target->is($request), $format);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param  array<string, mixed>  $data
     */
    public function testOf(bool $expect, string $format, array $data): void
    {
        if (! $expect) {
            static::assertTrue(true);

            return;
        }
        $target = new DatetimeTarget($format, 'period');
        $request = Request::create('/', 'GET', $data);
        static::assertInstanceOf(Carbon::class, $target->value($request));
    }
}
