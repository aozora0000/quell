<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereNotNull;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotNullTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 指定キーが存在する場合_isnotnullでクエリを実行する(): void
    {
        // deleted_atキーがfilledであることを条件にis not nullを適用
        $request = Request::create('/', 'GET', ['deleted_at' => '1']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereNotNull('deleted_at');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`deleted_at` IS NOT NULL
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'is not null クエリが一致しません');
    }
}
