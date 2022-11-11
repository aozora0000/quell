<?php

namespace Test\Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Promise\ThenCallableWithQuery;
use Querial\Promise\ThenWhereBetweenDaysWithQuery;
use Test\WithEloquentModelTestCase;

class ThenWhereBetweenDaysWithQueryTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['created_at_min' => '2022-01-01', 'created_at_max' => '2022-12-31']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetweenDaysWithQuery('created_at');
        dd($instance->resolve($request, $query));
        $this->assertSame(<<<EOT
select * from "users" where "name" LIKE 'test%'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }
}