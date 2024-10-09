<?php

namespace Querial\Promise;

use Illuminate\Http\Request;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereLessThanEqualTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    public function リクエストにキーが存在する場合LESSTHANEQUALクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['price' => '1']);

        $model = $this->createModel();
        $query = $model->newQuery();

        $instance = new ThenWhereLessThanEqual('price', null);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."price" <= '1'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $query)));
    }
}
