<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereGreaterThan;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereGreaterThanTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['price' => '1']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereGreaterThan('price', null);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."price" < '1'
EOT;
        $this->assertSame($sql, $this->format($instance->resolve($request, $query)));
    }
}
