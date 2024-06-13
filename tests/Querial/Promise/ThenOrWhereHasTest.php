<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenOrWhereHas;
use Querial\Promise\ThenWhereEqual;
use Tests\Querial\WithEloquentModelTestCase;

class ThenOrWhereHasTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    public function 複数のExistsサブクエリが入った時にORになる(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenOrWhereHas('items', new ThenPromisesAggregator([
            new ThenWhereEqual('name', null, 'users'),
        ])))->resolve($request, $query);
        $query = (new ThenOrWhereHas('items', new ThenPromisesAggregator([
            new ThenWhereEqual('email', null, 'users'),
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
  OR EXISTS (
    SELECT
      *
    FROM
      "items"
    WHERE
      "users"."id" = "items"."user_id"
      AND "users"."email" = 'email@email.com'
  )
EOT;
        $this->assertSame($sql, $this->format($query));
    }
}
