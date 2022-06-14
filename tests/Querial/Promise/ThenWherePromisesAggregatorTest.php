<?php

namespace Test\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereEqualWithQuery;
use Querial\Promise\ThenWhereLikeWithQuery;
use Querial\Promise\ThenWherePromisesAggregator;
use Test\Querial\WithEloquentModelTestCase;

class ThenWherePromisesAggregatorTest extends WithEloquentModelTestCase
{
    public function testResolve()
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWherePromisesAggregator([
            new ThenWhereEqualWithQuery('name'),
            new ThenWhereLikeWithQuery('email'),
        ]))->resolve($request, $query);
        $this->assertSame(<<<EOT
select * from "users" where ("users"."name" = 'test' and "users"."email" LIKE '%email@email.com%')
EOT
            , $query->toRawSql());
    }
}