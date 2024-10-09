<?php

namespace Tests\Querial\Promise\Support;

use Illuminate\Http\Request;
use Querial\Promise\Support\ThenOrWherePromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenOrWherePromisesAggregatorTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenOrWherePromisesAggregator([
            new ThenWhereEqual('name'),
        ]))->resolve($request, $query);
        $query = (new ThenOrWherePromisesAggregator([
            new ThenWhereLike('email'),
        ]))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  ("users"."name" = 'test')
  OR (
    "users"."email" LIKE '%email@email.com%'
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
