<?php

namespace Tests\Querial\Promise\Support;

use Illuminate\Http\Request;
use Querial\Promise\Support\ThenWherePromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWherePromisesAggregatorTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWherePromisesAggregator([
            new ThenWhereEqual('name'),
            new ThenWhereLike('email'),
        ]))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where ("users"."name" = 'test' and "users"."email" LIKE '%email@email.com%')
EOT
            , $query->toRawSql());
    }
}
