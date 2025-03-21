<?php

declare(strict_types=1);

namespace Tests\Querial\Target;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Querial\Target\ScalarTarget;

class ScalarTargetTest extends TestCase
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
        $target = new ScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertEquals($expect, $target->is($request));
    }

    /**
     * @dataProvider dataProvider
     *
     * @param  array<string, mixed>  $data
     */
    #[DataProvider('dataProvider')]
    public function test_of(bool $expect, array $data): void
    {
        $target = new ScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertIsBool($expect);
        static::assertEquals($target->value($request), $request->get('email'));
    }
}
