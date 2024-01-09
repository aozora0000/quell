<?php

namespace Test\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereHasRelation;
use Test\WithEloquentModelTestCase;

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
}
