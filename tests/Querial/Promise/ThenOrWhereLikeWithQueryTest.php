<?php

namespace Test\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenOrWhereLikeWithQuery;
use Test\WithEloquentModelTestCase;

class ThenOrWhereLikeWithQueryTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // 検索するテーブルを指定してクエリを作成する
        $query = (new ThenOrWhereLikeWithQuery('name'))->resolve($request, $query);
        $this->assertSame(<<<EOT
select * from "users" where "users"."name" LIKE '%test%'
EOT
            , $query->toRawSql());

        // 検索するテーブルを指定してクエリを作成する
        $query = (new ThenOrWhereLikeWithQuery('email'))->resolve($request, $query);
        $this->assertSame(<<<EOT
select * from "users" where "users"."name" LIKE '%test%' or "users"."email" LIKE '%email@email.com%'
EOT
            , $query->toRawSql());

        // リクエストに存在しないキーの場合、SQLには反映されない
        $query = (new ThenOrWhereLikeWithQuery('onattr'))->resolve($request, $query);
        $this->assertNotSame(<<<EOT
select * from "users" where "users"."name" LIKE '%test%' or "users"."email" LIKE '%email@email.com%' or "users"."noattr" LIKE ''
EOT
            , $query->toRawSql());
    }
}