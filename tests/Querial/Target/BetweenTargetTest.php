<?php

namespace Tests\Querial\Target;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Querial\Target\BetweenTarget;
use Querial\Target\ScalarTarget;

class BetweenTargetTest extends TestCase
{
    /**
     * @return array<array{bool,array<string, mixed>}>
     */
    public static function dataProvider(): array
    {
        return [
            [
                true,
                [
                    'from' => 1,
                    'to' => 3,
                ],
            ],
            [
                false,
                [
                    'from' => 1,
                    'to' => '',
                ],
            ],
            [
                false,
                [
                    'from' => '',
                    'to' => 3,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param  array<string, mixed>  $data
     */
    #[DataProvider('dataProvider')]
    public function test_is(bool $expect, array $data): void
    {
        $target = new BetweenTarget(new ScalarTarget('from'), new ScalarTarget('to'));
        $request = Request::create('/', 'GET', $data);
        $this->assertEquals($expect, $target->is($request), implode(': ', $target->value($request)));
    }

    public function test_of(): void
    {
        $target = new BetweenTarget(new ScalarTarget('from'), new ScalarTarget('to'));

        $request = Request::create('/', 'GET', ['from' => 1, 'to' => 3]);
        $this->assertSame(['1', '3'], $target->value($request));

        $request = Request::create('/', 'GET', ['from' => 3, 'to' => 1]);
        $this->assertSame(['1', '3'], $target->value($request));
        $this->assertNotSame(['3', '1'], $target->value($request));

        $request = Request::create('/', 'GET', ['from' => null, 'to' => 1]);
        $this->assertSame(['', '1'], $target->value($request));
    }
}
