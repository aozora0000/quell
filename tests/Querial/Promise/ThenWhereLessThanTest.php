<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereLessThan;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereLessThanTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['price' => '1']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereLessThan('price', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."price" > '1'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }
}
