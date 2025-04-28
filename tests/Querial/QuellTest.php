<?php

namespace Tests\Querial;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\PromiseInterface;
use Querial\Promise\Support\ThenWherePromisesAggregator;
use Querial\Promise\ThenWhereBetween;
use Querial\Promise\ThenWhereEqual;
use Querial\Quell;

class QuellTest extends WithEloquentModelTestCase
{
    public function test_build(): void
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
                    new ThenWhereEqual('name'),
                    new ThenWhereBetween('created_at'),
                ]);
            }
        };

        $query = $this->createModel()->newQuery();
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  (
    `users`.`name` = 'test'
    AND `users`.`created_at` BETWEEN '2022-01-01'
    AND '2022-12-31'
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->build($query)));

    }

    public function test_default_build(): void
    {
        $request = Request::create('/', 'GET');

        $instance = new class($request) extends Quell
        {
            protected function default(): ?callable
            {
                return static function (Request $request, EloquentBuilder|QueryBuilder $builder) {
                    $builder->limit(10);
                };
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
                    new ThenWhereEqual('name'),
                    new ThenWhereBetween('created_at'),
                ]);
            }
        };

        $query = $this->createModel()->newQuery();
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
LIMIT
  10
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->build($query)));
    }

    public function test_failed_build(): void
    {
        $request = Request::create('/', 'GET');

        $instance = new class($request) extends Quell
        {
            protected function failed(): ?callable
            {
                return static function (Request $request, QueryBuilder|EloquentBuilder $builder, \Throwable $throwable) {
                    $builder->where('name', $throwable->getMessage());

                    return $builder;
                };
            }

            protected function finally(): ?callable
            {
                return null;
            }

            protected function promise(): ?PromiseInterface
            {
                return new class implements PromiseInterface
                {
                    public function match(Request $request): bool
                    {
                        return true;
                    }

                    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
                    {
                        throw new \RuntimeException('unknown');
                    }
                };
            }
        };

        $query = $this->createModel()->newQuery();
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `name` = 'unknown'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->build($query)));
    }
}
