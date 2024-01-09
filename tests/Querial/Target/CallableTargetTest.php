<?php

namespace Test\Querial\Target;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Querial\Target\CallableTarget;

class CallableTargetTest extends TestCase
{
    public function testIs(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $target = new CallableTarget(static function (Request $request) {
            return $request->query('name') === 'test';
        }, static function (Request $request) {
            return 'overwrite';
        });
        $this->assertTrue($target->is($request));
        $this->assertSame('overwrite', $target->value($request));
    }
}
