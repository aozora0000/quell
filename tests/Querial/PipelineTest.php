<?php

namespace Tests\Querial;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Querial\Contracts\PromiseInterface;
use Querial\Pipeline;
use ReflectionClass;

class PipelineTest extends TestCase
{
    /**
     * @test
     * Promiseを追加できることを確認します
     */
    #[Test]
    public function 追加したpromiseが取得できることを確認()
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);
        $promise = $this->createMock(PromiseInterface::class);

        $pipeline->then($promise);

        $reflection = new ReflectionClass($pipeline);
        $property = $reflection->getProperty('promises');
        $property->setAccessible(true);
        $promises = $property->getValue($pipeline);

        $this->assertCount(1, $promises);
        $this->assertSame($promise, $promises[0]);
    }

    /**
     * @test
     * onFailedクロージャを設定できることを確認します
     */
    #[Test]
    public function on_failedクロージャが設定できることを確認()
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);
        $callback = function () {};

        $pipeline->onFailed($callback);

        $reflection = new ReflectionClass($pipeline);
        $property = $reflection->getProperty('onFailedClosure');
        $property->setAccessible(true);
        $closure = $property->getValue($pipeline);

        $this->assertSame($callback, $closure);
    }

    /**
     * @test
     * 例外が発生した場合にonFailedクロージャが呼び出されることを確認します
     */
    #[Test]
    public function 例外発生時にon_failedクロージャが呼び出されることを確認()
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);
        $promise = $this->createMock(PromiseInterface::class);

        $promise->method('match')->willReturn(true);
        $promise->method('resolve')->willThrowException(new \Exception('Test Exception'));

        $pipeline->then($promise);

        $onFailedCalled = false;
        $pipeline->onFailed(function () use (&$onFailedCalled) {
            $onFailedCalled = true;
        });

        $builder = $this->createMock(EloquentBuilder::class);

        try {
            $pipeline->build($builder);
        } catch (\Exception $e) {
            // 無視
        }

        $this->assertTrue($onFailedCalled);
    }

    /**
     * @test
     * onFinallyクロージャが呼び出されることを確認します
     */
    #[Test]
    public function on_finallyクロージャが呼び出されることを確認()
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);

        $onFinallyCalled = false;
        $pipeline->onFinally(function () use (&$onFinallyCalled) {
            $onFinallyCalled = true;
        });

        $builder = $this->createMock(EloquentBuilder::class);

        $pipeline->build($builder);

        $this->assertTrue($onFinallyCalled);
    }

    /**
     * @test
     * onDefaultクロージャがデフォルトの場合に呼び出されることを確認します
     */
    #[Test]
    public function on_defaultクロージャがデフォルトで呼び出されることを確認()
    {
        $request = Request::create('/', 'GET');
        $pipeline = new Pipeline($request);

        $onDefaultCalled = false;
        $pipeline->onDefault(function () use (&$onDefaultCalled) {
            $onDefaultCalled = true;
        });

        $builder = $this->createMock(EloquentBuilder::class);

        $pipeline->build($builder);

        $this->assertTrue($onDefaultCalled);
    }
}
