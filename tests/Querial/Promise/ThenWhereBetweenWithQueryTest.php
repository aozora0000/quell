<?php

namespace Test\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereBetweenWithQuery;
use Test\WithEloquentModelTestCase;

class ThenWhereBetweenWithQueryTest extends WithEloquentModelTestCase
{
    public function testResolveMinMax(): void
    {
        $request = Request::create('/', 'GET', ['price_min' => '1', 'price_max' => '100']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetweenWithQuery('price', null);
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

        $instance = new ThenWhereBetweenWithQuery('price', null);
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

        $instance = new ThenWhereBetweenWithQuery('price', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."price" <= '100'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }
}
