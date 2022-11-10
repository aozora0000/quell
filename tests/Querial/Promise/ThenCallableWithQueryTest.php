<?php

namespace Test\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereEqualWithQuery;
use Test\WithEloquentModelTestCase;

class ThenCallableWithQueryTest extends WithEloquentModelTestCase
{

    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // TODO: ここから
        $query = (new ThenWhereEqualWithQuery('name'))->resolve($request, $query);
        $this->assertSame(<<<EOT
select * from "users" where "users"."name" = 'test'
EOT
            , $query->toRawSql());
    }
}