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
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."price" BETWEEN '1'
  AND '100'
EOT;
        $this->assertSame($sql, $this->format($instance->resolve($request, $query)));
    }

    public function testResolveMinOnly(): void
    {
        $request = Request::create('/', 'GET', ['price_min' => '1']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetween('price', null);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."price" >= '1'
EOT;
        $this->assertSame($sql, $this->format($instance->resolve($request, $query)));
    }

    public function testResolveMaxOnly(): void
    {
        $request = Request::create('/', 'GET', ['price_max' => '100']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereBetween('price', null);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."price" <= '100'
EOT;
        $this->assertSame($sql, $this->format($instance->resolve($request, $query)));
    }
}
