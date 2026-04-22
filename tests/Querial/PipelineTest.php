<?php

declare(strict_types=1);

namespace Tests\Querial;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Querial\Contracts\PromiseInterface;
use Querial\Pipeline;
use ReflectionClass;

final class PipelineTest extends TestCase
{
    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function 追加したpromiseが取得できることを確認(): void
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);
        $promise = $this->createStub(PromiseInterface::class);

        $pipeline->then($promise);

        $reflection = new ReflectionClass($pipeline);
        $reflectionProperty = $reflection->getProperty('promises');

        $promises = $reflectionProperty->getValue($pipeline);

        $this->assertCount(1, $promises);
        $this->assertSame($promise, $promises[0]);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function on_failedクロージャが設定できることを確認(): void
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);
        $callback = function (): void {};

        $pipeline->onFailed($callback);

        $reflection = new ReflectionClass($pipeline);
        $reflectionProperty = $reflection->getProperty('onFailedClosure');

        $closure = $reflectionProperty->getValue($pipeline);

        $this->assertSame($callback, $closure);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function 例外発生時にon_failedクロージャが呼び出されることを確認(): void
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);
        $promise = $this->createMock(PromiseInterface::class);

        $promise->method('match')->willReturn(true);
        $promise->method('resolve')->willThrowException(new \Exception('Test Exception'));

        $pipeline->then($promise);

        $onFailedCalled = false;
        $pipeline->onFailed(function () use (&$onFailedCalled): void {
            $onFailedCalled = true;
        });

        $builder = $this->createStub(EloquentBuilder::class);

        try {
            $pipeline->build($builder);
        } catch (\Exception) {
            // 無視
        }

        $this->assertTrue($onFailedCalled);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function on_finallyクロージャが呼び出されることを確認(): void
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);

        $onFinallyCalled = false;
        $pipeline->onFinally(function () use (&$onFinallyCalled): void {
            $onFinallyCalled = true;
        });

        $builder = $this->createStub(EloquentBuilder::class);

        $pipeline->build($builder);

        $this->assertTrue($onFinallyCalled);
    }

    #[Test]
    #[AllowMockObjectsWithoutExpectations]
    public function on_defaultクロージャがデフォルトで呼び出されることを確認(): void
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);

        $onDefaultCalled = false;
        $pipeline->onDefault(function () use (&$onDefaultCalled): void {
            $onDefaultCalled = true;
        });

        $builder = $this->createStub(EloquentBuilder::class);

        $pipeline->build($builder);

        $this->assertTrue($onDefaultCalled);
    }
}
