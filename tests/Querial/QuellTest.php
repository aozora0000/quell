<?php

namespace Test\Querial;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;
use Querial\Promise\ThenWhereBetweenWithQuery;
use Querial\Promise\ThenWhereEqualWithQuery;
use Querial\Promise\ThenWherePromisesAggregator;
use Querial\Quell;
use Test\WithEloquentModelTestCase;

class QuellTest extends WithEloquentModelTestCase
{
    public function testBuild(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'created_at_min' => '2022-01-01', 'created_at_max' => '2022-12-31']);

        $instance = new class($request) extends Quell
        {
            protected function failed(): ?callable
            {
                return null;
            }

            protected function finally(): ?callable
            {
                return null;
            }

            protected function promise(): ?PromiseInterface
            {
                return new ThenWherePromisesAggregator([
                    new ThenWhereEqualWithQuery('name'),
                    new ThenWhereBetweenWithQuery('created_at'),
                ]);
            }
        };

        $query = $this->createModel()->newQuery();
        $this->assertSame(<<<'EOT'
select * from "users" where ("users"."name" = 'test' and "users"."created_at" between '2022-01-01' and '2022-12-31')
EOT
            , $instance->build($query)->toRawSql());
    }

    public function testDefaultBuild(): void
    {
        $request = Request::create('/', 'GET');

        $instance = new class($request) extends Quell
        {
            protected function default(EloquentBuilder|QueryBuilder $builder): EloquentBuilder|QueryBuilder|null
            {
                return $builder->limit(10);
            }

            protected function failed(): ?callable
            {
                return null;
            }

            protected function finally(): ?callable
            {
                return null;
            }

            protected function promise(): ?PromiseInterface
            {
                return new ThenWherePromisesAggregator([
                    new ThenWhereEqualWithQuery('name'),
                    new ThenWhereBetweenWithQuery('created_at'),
                ]);
            }
        };

        $query = $this->createModel()->newQuery();
        $this->assertSame(<<<'EOT'
select * from "users" limit 10
EOT
            , $instance->build($query)->toRawSql());
    }
}
