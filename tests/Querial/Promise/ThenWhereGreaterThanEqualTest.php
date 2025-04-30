<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereGreaterThanEqual;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereGreaterThanEqualTest extends WithEloquentModelTestCase
{
    #[Test]
    public function リクエストにキーが存在する場合_greaterthanequalクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['price' => '1']);

        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereGreaterThanEqual('price', null);
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`price` >= '1'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $builder)));
    }
}
