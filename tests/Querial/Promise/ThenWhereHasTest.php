<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereHas;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereHasTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereHas('items'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  EXISTS (
    SELECT
      *
    FROM
      "items"
    WHERE
      "users"."id" = "items"."user_id"
  )
EOT;
        $this->assertSame($sql, $this->format($query));
    }

    public function testResolveWithSubWhereQuery(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereHas('items', new ThenPromisesAggregator([
            new ThenWhereEqual('name', null, 'users'),
        ])))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  EXISTS (
    SELECT
      *
    FROM
      "items"
    WHERE
      "users"."id" = "items"."user_id"
      AND "users"."name" = 'test'
  )
EOT;
        $this->assertSame($sql, $this->format($query));
    }
}
