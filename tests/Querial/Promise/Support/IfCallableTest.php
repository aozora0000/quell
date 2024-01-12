<?php

namespace Tests\Querial\Promise\Support;

use Illuminate\Http\Request;
use Querial\Promise\Support\IfCallable;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class IfCallableTest extends WithEloquentModelTestCase
{
    public function testResolved(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new IfCallable(function (Request $request) {
            return $request->filled('name') && $request->input('name') === 'test';
        }, new ThenPromisesAggregator([
            new ThenWhereEqual('name'),
            new ThenWhereLike('email'),
        ])))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" = 'test' and "users"."email" LIKE '%email@email.com%'
EOT
            , $query->toRawSql());
    }

    public function testNotResolved(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new IfCallable(function (Request $request) {
            return $request->filled('name') && $request->input('name') === 'test2';
        }, new ThenPromisesAggregator([
            new ThenWhereEqual('name'),
            new ThenWhereLike('email'),
        ])))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users"
EOT
            , $query->toRawSql());
    }
}
