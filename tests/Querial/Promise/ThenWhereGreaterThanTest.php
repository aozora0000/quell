<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereGreaterThan;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereGreaterThanTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    #[Test]
    public function リクエストにキーが存在する場合_greatertha_nクエリを発行する事を確認(): void
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
  "users"."price" > '1'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $query)));
    }
}
