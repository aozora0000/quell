<?php

namespace Test\Querial;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;
use Querial\Promise\ThenWhereEqualWithQuery;
use Querial\Quell;
use Test\WithEloquentModelTestCase;

class QuellTest extends WithEloquentModelTestCase
{
    public function testBuild()
    {
        $request = Request::create('/', 'GET', ['value' => '1']);

        $instance = new class($request) extends Quell {

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
                return new ThenWhereEqualWithQuery('value');
            }
        };

        $query = $this->createModel()->newQuery();
        $this->assertSame(<<<EOT
select * from "users" where "users"."value" = 1
EOT
            , $instance->build($query)->toRawSql());
    }

    public function testDefaultBuild()
    {
        $request = Request::create('/', 'GET');

        $instance = new class($request) extends Quell {
            protected function default(Builder $builder): Builder
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
                return new ThenWhereEqualWithQuery('value');
            }
        };

        $query = $this->createModel()->newQuery();
        $this->assertSame(<<<EOT
select * from "users" limit 10
EOT
            , $instance->build($query)->toRawSql());
    }
}