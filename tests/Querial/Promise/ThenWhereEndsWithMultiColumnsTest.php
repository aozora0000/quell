<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereEndsWithMultiColumns;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereEndsWithMultiColumnsTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 単一キーワードで複数カラムの後方一致_or_likeが適用される(): void
    {
        // q="bar" に対して name と email の両方に '%bar' のOR条件が構築される
        $request = Request::create('/', 'GET', ['q' => 'bar']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereEndsWithMultiColumns(['name', 'email'], 'q');

        // 1回目の解決で where 句が1セット追加される
        $sql = $this->format($instance->resolve($request, $builder));

        $expected = <<<'EOT'
select
  *
from
  `users`
where
  (
    `users`.`name` like '%bar'
    or `users`.`email` like '%bar'
  )
  and (
    `users`.`name` like '%bar'
    or `users`.`email` like '%bar'
  )
EOT;
        // 2回目の解決で同じ where 句がもう1セット追加され、期待SQLと一致する
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '後方一致の複数カラムOR条件が期待通りではありません');
    }
}
