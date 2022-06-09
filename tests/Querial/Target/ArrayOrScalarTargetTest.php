<?php declare(strict_types=1);
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
     *
     * @param bool  $expect
     * @param array $data
     */
    public function testIs(bool $expect, array $data): void
    {
        $target  = new ArrayOrScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertEquals($expect, $target->is($request));
    }


    public function testOf(): void
    {
        $target = new ArrayOrScalarTarget('email');

        $data    = ['email' => '@'];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals(['@'], $target->of($request));

        $data = [
            'email' => [
                '@',
                '@',
            ],
        ];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals(['@', '@'], $target->of($request));

        $data    = ['email' => ''];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals([], $target->of($request));

        $data    = [];
        $request = Request::create('/', 'GET', $data);
        static::assertEquals([], $target->of($request));
    }
}
