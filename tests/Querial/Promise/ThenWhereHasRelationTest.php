<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereHasRelation;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereHasRelationTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereHasRelation('items'))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where exists (select * from "items" where "users"."id" = "items"."user_id")
EOT
            , $query->toRawSql());
    }

    public function testResolveWithSubWhereQuery(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereHasRelation('items', new ThenPromisesAggregator([
            new ThenWhereEqual('name', null, 'users'),
        ])))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where exists (select * from "items" where "users"."id" = "items"."user_id" and "users"."name" = 'test')
EOT
            , $query->toRawSql());
    }
}
