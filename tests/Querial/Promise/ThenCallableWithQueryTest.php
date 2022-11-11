<?php

namespace Test\Querial\Promise;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Promise\ThenCallableWithQuery;
use Querial\Promise\ThenWhereEqualWithQuery;
use Test\WithEloquentModelTestCase;

class ThenCallableWithQueryTest extends WithEloquentModelTestCase
{

    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = (new ThenCallableWithQuery(function (Request $request) {
            return $request->has('name') && $request->input('name') === 'test';
        }, function (Request $request, Builder $builder) {
            return $builder->where('name', 'LIKE', 'test%');
        }));
        $this->assertTrue($instance->resolveIf($request));
        $this->assertSame(<<<EOT
select * from "users" where "name" LIKE 'test%'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }
}