<?php

declare(strict_types=1);

namespace Tests\Querial\Target;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Querial\Target\ArrayOrScalarTarget;

class ArrayOrScalarTargetTest extends TestCase
{
    /**
     * @return array<array{bool,array<string, mixed>}>
     */
    public static function dataProvider(): array
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
     *
     * @param  array<string, mixed>  $data
     */
    #[DataProvider('dataProvider')]
    public function test_is(bool $expect, array $data): void
    {
        $target = new ArrayOrScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertEquals($expect, $target->is($request));
    }

    public function test_of(): void
    {
        $target = new ArrayOrScalarTarget('email');

        $data = ['email' => '@'];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals(['@'], $target->value($request));

        $data = [
            'email' => [
                '@',
                '@',
            ],
        ];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals(['@', '@'], $target->value($request));

        $data = ['email' => ''];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals([], $target->value($request));

        $data = [];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals([], $target->value($request));
    }
}
