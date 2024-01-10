<?php

namespace Tests\Querial\Promise;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Querial\Promise\ThenWhereEqual;
use Tests\Querial\WithEloquentModelTestCase;

/**
 * @property Builder $builder
 */
class ThenWhereEqualTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // リクエストに存在するキーでwhereを掛ける
        $query = (new ThenWhereEqual('name'))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" = 'test'
EOT
            , $query->toRawSql());

        // リクエストに存在するキーでand whereを掛ける
        $query = (new ThenWhereEqual('email'))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" = 'test' and "users"."email" = 'email@email.com'
EOT
            , $query->toRawSql());

        // リクエストに存在しないキーの場合、SQLには反映されない
        $query = (new ThenWhereEqual('noattr'))->resolve($request, $query);
        $this->assertNotSame(<<<'EOT'
select * from "users" where "users"."name" = 'test' and "users"."email" = 'email@email.com' and "users"."noattr" = ''
EOT
            , $query->toRawSql());
    }

    public function testResolveAnotherTable(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // 検索するテーブルを指定してクエリを作成する
        $query = (new ThenWhereEqual('name', null, 'items'))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "items"."name" = 'test'
EOT
            , $query->toRawSql());
    }
}
