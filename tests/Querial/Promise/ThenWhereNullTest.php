<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereNull;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNullTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 指定キーが存在する場合_isnullでクエリを実行する(): void
    {
        // deleted_atキーがfilledであることを条件にis nullを適用
        $request = Request::create('/', 'GET', ['deleted_at' => '1']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereNull('deleted_at');

        // 期待するSQL（小文字比較のために大文字で記述）
        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`deleted_at` IS NULL
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'is null クエリが一致しません');
    }
}
