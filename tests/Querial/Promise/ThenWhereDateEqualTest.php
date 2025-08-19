<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereDateEqual;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereDateEqualTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 指定日付が存在する場合_同一日の範囲でクエリを実行する(): void
    {
        // created_atに対して、入力キーはdateを使う
        $request = Request::create('/', 'GET', ['date' => '2022-01-10']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereDateEqual('created_at', 'date', 'Y-m-d');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`created_at` BETWEEN '2022-01-10 00:00:00'
  AND '2022-01-10 23:59:59'
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '同一日範囲のクエリが一致しません');
    }
}
