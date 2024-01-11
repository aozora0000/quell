<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereBetweenDays;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereBetweenDaysTest extends WithEloquentModelTestCase
{
    public function testResolveMinMax(): void
    {
        $request = Request::create('/', 'GET', ['created_at_min' => '2022-01-01', 'created_at_max' => '2022-12-31']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetweenDays('created_at', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."created_at" between '2022-01-01' and '2022-12-31'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }

    public function testResolveMinOnly(): void
    {
        $request = Request::create('/', 'GET', ['created_at_min' => '2022-01-01']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetweenDays('created_at', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."created_at" >= '2022-01-01'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }

    public function testResolveMaxOnly(): void
    {
        $request = Request::create('/', 'GET', ['created_at_max' => '2022-12-31']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetweenDays('created_at', null);
        $this->assertSame(<<<'EOT'
select * from "users" where "users"."created_at" <= '2022-12-31'
EOT
            , $instance->resolve($request, $query)->toRawSql());
    }
}
