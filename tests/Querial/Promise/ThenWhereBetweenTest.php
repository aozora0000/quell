<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereBetween;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereBetweenTest extends WithEloquentModelTestCase
{
    public function testResolveMinMax(): void
    {
        $request = Request::create('/', 'GET', ['price_min' => '1', 'price_max' => '100']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetween('price', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."price" between '1' and '100'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }

    public function testResolveMinOnly(): void
    {
        $request = Request::create('/', 'GET', ['price_min' => '1']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetween('price', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."price" >= '1'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }

    public function testResolveMaxOnly(): void
    {
        $request = Request::create('/', 'GET', ['price_max' => '100']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetween('price', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."price" <= '100'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }
}
