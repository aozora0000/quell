<?php

namespace Tests\Querial\Promise\Support;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\Support\IfCallable;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class IfCallableTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    #[Test]
    public function 通常関数の条件に一致した場合、promiseクエリが実行される(): void
    {
        $request = Request::create('/', 'GET', ['mode' => 'search', 'name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new IfCallable('is_object', new ThenPromisesAggregator([
            new ThenWhereEqual('name'),
            new ThenWhereLike('email'),
        ])))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" = 'test'
  AND "users"."email" LIKE '%email@email.com%'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    /**
     * @test
     */
    #[Test]
    public function 即時関数の条件に一致した場合、_promiseクエリが実行される(): void
    {
        $request = Request::create('/', 'GET', ['mode' => 'search', 'name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new IfCallable(function (Request $request) {
            return $request->filled('mode') && $request->input('mode') === 'search';
        }, new ThenPromisesAggregator([
            new ThenWhereEqual('name'),
            new ThenWhereLike('email'),
        ])))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" = 'test'
  AND "users"."email" LIKE '%email@email.com%'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    /**
     * @test
     */
    #[Test]
    public function 即時関数の条件に一致しない場合、_promiseクエリは実行されない(): void
    {
        $request = Request::create('/', 'GET', ['mode' => 'normal', 'name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new IfCallable(function (Request $request) {
            return $request->filled('name') && $request->input('name') === 'search';
        }, new ThenPromisesAggregator([
            new ThenWhereEqual('name'),
            new ThenWhereLike('email'),
        ])))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
