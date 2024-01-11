<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Formatter\LikeFormatter;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereLikeTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereLike('name'))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" LIKE '%test%'
EOT
            , $query->toRawSql());

        $query = (new ThenWhereLike('email', null, null, LikeFormatter::BACKWARD_MATCH))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" LIKE '%test%' and "users"."email" LIKE 'email@email.com%'
EOT
            , $query->toRawSql());
    }
}
