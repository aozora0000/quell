<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereNotInArray;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotInArrayTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => ['test1', 'test2'], 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotInArray('name'))->resolve($request, $query);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."name" not in ('test1', 'test2')
EOT
            , $query->toRawSql());
    }
}
