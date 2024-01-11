<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Formatter\LikeFormatter;
use Querial\Promise\ThenWhereNotLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotLikeTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotLike('name'))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" not LIKE '%test%'
EOT
            , $query->toRawSql());

        $query = (new ThenWhereNotLike('email', null, null, LikeFormatter::BACKWARD_MATCH))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" not LIKE '%test%' and "users"."email" not LIKE '%email@email.com'
EOT
            , $query->toRawSql());

        $query = (new ThenWhereNotLike('email', null, null, LikeFormatter::FORWARD_MATCH))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" not LIKE '%test%' and "users"."email" not LIKE '%email@email.com' and "users"."email" not LIKE 'email@email.com%'
EOT
            , $query->toRawSql());
    }
}
