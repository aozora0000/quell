<?php

namespace Tests\Querial\Target;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Querial\Target\CallableTarget;

class CallableTargetTest extends TestCase
{
    public function test_is(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $target = new CallableTarget(static fn (Request $request): bool => $request->query('name') === 'test', static fn (Request $request): string => 'overwrite');
        $this->assertTrue($target->is($request));
        $this->assertSame('overwrite', $target->value($request));
    }
}
