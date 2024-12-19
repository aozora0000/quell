<?php

namespace Tests\Querial\Promise\Support;

use Illuminate\Http\Request;
use Querial\Promise\Support\ThenWherePromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWherePromisesAggregatorTest extends WithEloquentModelTestCase
{
    public function test_resolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWherePromisesAggregator([
            new ThenWhereEqual('name'),
            new ThenWhereLike('email'),
        ]))->resolve($request, $query);

        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  (
    "users"."name" = 'test'
    and "users"."email" like '%email@email.com%'
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
