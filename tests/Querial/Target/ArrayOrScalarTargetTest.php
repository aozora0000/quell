<?php

namespace Querial\Target;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ArrayOrScalarTargetTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            [
                true,
                ['email' => '@'],
            ],
            [
                true,
                ['email' => [
                    '@',
                    '@',
                ]],
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
        $target = new ArrayOrScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertEquals($expect, $target->isTarget($request));
    }

    /**
     */
    public function testGetTarget(): void
    {
        $target = new ArrayOrScalarTarget('email');

        $data = ['email' => '@'];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals(['@'], $target->getTarget($request));

        $data = [
            'email' => [
                '@',
                '@',
            ],
        ];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals(['@', '@'], $target->getTarget($request));

        $data = ['email' => ''];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals([], $target->getTarget($request));

        $data = [];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals([], $target->getTarget($request));
    }
}