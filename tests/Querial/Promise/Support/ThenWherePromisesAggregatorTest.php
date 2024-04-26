<?php

namespace Tests\Querial\Promise\Support;

use Illuminate\Http\Request;
use Querial\Promise\Support\ThenWherePromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWherePromisesAggregatorTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWherePromisesAggregator([
            new ThenWhereEqual('name'),
        ]))->resolve($request, $query);
        $query = (new ThenWherePromisesAggregator([
            new ThenWhereLike('email'),
        ]))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  ("users"."name" = 'test')
  AND (
    "users"."email" LIKE '%email@email.com%'
  )
EOT;
        $this->assertSame($sql, $this->format($query));
    }
}
