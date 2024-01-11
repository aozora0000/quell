<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereGreaterThan;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereGreaterThanTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['price' => '1']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereGreaterThan('price', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."price" < '1'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }
}
