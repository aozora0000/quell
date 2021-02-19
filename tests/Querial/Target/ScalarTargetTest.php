<?php

namespace Querial\Test\Target;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Querial\Target\ScalarTarget;

class ScalarTargetTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [
                true,
                ['email' => '@'],
            ],
            [
                false,
                ['email' => ''],
            ],
            [
                false,
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param bool  $expect
     * @param array $data
     */
    public function testIsTarget(bool $expect, array $data): void
    {
        $target  = new ScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertEquals($expect, $target->isTarget($request));
    }

    /**
     * @dataProvider dataProvider
     * @param bool  $expect
     * @param array $data
     */
    public function testGetTarget(bool $expect, array $data): void
    {
        $target  = new ScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertIsBool($expect);
        static::assertEquals($target->getTarget($request), $request->get('email'));
    }
}