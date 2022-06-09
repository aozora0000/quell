<?php

namespace Querial\Target;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class BetweenTargetTest extends TestCase
{
    protected function dataProvider(): array
    {
        return [
            [
                true,
                [
                    'from' => 1,
                    'to' => 3,
                ]
            ],
            [
                false,
                [
                    'from' => 1,
                    'to' => '',
                ]
            ],
            [
                false,
                [
                    'from' => '',
                    'to' => 3,
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param bool  $expect
     * @param array $data
     * @return void
     */
    public function testIs(bool $expect, array $data): void
    {
        $target = new BetweenTarget(new ScalarTarget('from'), new ScalarTarget('to'));
        $request = Request::create('/', 'GET', $data);
        $this->assertEquals($expect, $target->is($request), implode(': ', $target->of($request)));
    }

    public function testOf(): void
    {
        $target = new BetweenTarget(new ScalarTarget('from'), new ScalarTarget('to'));

        $request = Request::create('/', 'GET', ['from' => 1, 'to' => 3]);
        $this->assertSame([1, 3], $target->of($request));

        $request = Request::create('/', 'GET', ['from' => 3, 'to' => 1]);
        $this->assertSame([1, 3], $target->of($request));
        $this->assertNotSame([3, 1], $target->of($request));

        $request = Request::create('/', 'GET', ['from' => null, 'to' => 1]);
        $this->assertSame([null, 1], $target->of($request));
    }
}