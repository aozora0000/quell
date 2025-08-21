<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereStartsWithMultiColumns;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereStartsWithMultiColumnsTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 単一キーワードで複数カラムの前方一致_or_likeが適用される(): void
    {
        // q="foo" に対して name と email の両方に 'foo%' のOR条件が構築される
        $request = Request::create('/', 'GET', ['q' => 'foo']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereStartsWithMultiColumns(['name', 'email'], 'q');

        // 1回目の解決で where 句が1セット追加される
        $sql = $this->format($instance->resolve($request, $builder));

        $expected = <<<'EOT'
select
  *
from
  `users`
where
  (
    `users`.`name` like 'foo%'
    or `users`.`email` like 'foo%'
  )
  and (
    `users`.`name` like 'foo%'
    or `users`.`email` like 'foo%'
  )
EOT;
        // 2回目の解決で同じ where 句がもう1セット追加され、期待SQLと一致する
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '前方一致の複数カラムOR条件が期待通りではありません');
    }
}
