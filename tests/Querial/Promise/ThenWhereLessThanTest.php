<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereLessThan;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereLessThanTest extends WithEloquentModelTestCase
{
    /**
     * @test
     * @return void
     */
    public function リクエストにキーが存在する場合LESSTHANクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['price' => '1']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereLessThan('price', null);
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
