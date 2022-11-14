<?php

namespace Test\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Formatter\LikeFormatter;
use Querial\Promise\ThenWhereLikeWithQuery;
use Test\WithEloquentModelTestCase;

class ThenWhereLikeWithQueryTest extends WithEloquentModelTestCase
{
    public function testResolve()
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereLikeWithQuery('name'))->resolve($request, $query);
        $this->assertSame(<<<EOT
select * from "users" where "users"."name" LIKE '%test%'
EOT
            , $query->toRawSql());


        $query = (new ThenWhereLikeWithQuery('email', null, null, LikeFormatter::BACKWORD_MATCH))->resolve($request, $query);
        $this->assertSame(<<<EOT
select * from "users" where "users"."name" LIKE '%test%' and "users"."email" LIKE 'email@email.com%'
EOT
            , $query->toRawSql());
    }
}