<?php

namespace Tests\Querial\Target;

use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Querial\Target\DatetimeTarget;

class DatetimeTargetTest extends TestCase
{
    /**
     * @return \Iterator<(int | string), array{bool, string, array<string, mixed>}>
     */
    public static function dataProvider(): \Iterator
    {
        yield [
            true,
            'Y-m-d H:i:s',
            [
                'period' => '2020-01-01 00:00:00',
            ],
        ];
        yield [
            true,
            'Y-m-d',
            [
                'period' => '2020-01-01',
            ],
        ];
        yield [
            false,
            'Y-m-d H:i:s',
            [
                'period' => '2020-01-01',
            ],
        ];
        yield [
            true,
            'Y/m/d',
            [
                'period' => '2020/01/01',
            ],
        ];
        yield [
            false,
            'Y-m-d',
            [
                'period' => '2020/01/01',
            ],
        ];
        yield [
            true,
            'U',
            [
                'period' => '1577836800',
            ],
        ];
        yield [
            // TODO: 12月扱いになるが許容すべきなのか？Datetimeもstrtotimeに準拠する？
            true,
            'Y-m-d H:i:s',
            [
                'period' => '2020-00-01 00:00:00',
            ],
        ];
        yield [
            true,
            'H:i:s',
            [
                'period' => '00:00:00',
            ],
        ];
        yield [
            false,
            'Y-m-d H:i:s',
            [
                'period' => 'testetst',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param  array<string, mixed>  $data
     */
    #[DataProvider('dataProvider')]
    public function test_is(bool $expect, string $format, array $data): void
    {
        $target = new DatetimeTarget($format, 'period');
        $request = Request::create('/', 'GET', $data);
        $this->assertSame($expect, $target->is($request), $format);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param  array<string, mixed>  $data
     */
    #[DataProvider('dataProvider')]
    public function test_of(bool $expect, string $format, array $data): void
    {
        if (! $expect) {
            $this->assertTrue(true);

            return;
        }

        $target = new DatetimeTarget($format, 'period');
        $request = Request::create('/', 'GET', $data);
        $this->assertInstanceOf(Carbon::class, $target->value($request));
    }
}
