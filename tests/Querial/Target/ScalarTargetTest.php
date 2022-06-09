<?php declare(strict_types=1);
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
     *
     * @param bool  $expect
     * @param array $data
     */
    public function testIs(bool $expect, array $data): void
    {
        $target  = new ScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertEquals($expect, $target->is($request));
    }

    /**
     * @dataProvider dataProvider
     *
     * @param bool  $expect
     * @param array $data
     */
    public function testOf(bool $expect, array $data): void
    {
        $target  = new ScalarTarget('email');
        $request = Request::create('/', 'GET', $data);
        static::assertIsBool($expect);
        static::assertEquals($target->of($request), $request->get('email'));
    }
}
