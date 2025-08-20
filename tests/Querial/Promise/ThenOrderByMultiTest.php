<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenOrderByMulti;
use Tests\Querial\WithEloquentModelTestCase;

class ThenOrderByMultiTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 複数指定の優先順で_order_byが適用される(): void
    {
        // sort='-created_at,name' → ORDER BY created_at desc, name asc
        $request = Request::create('/', 'GET', ['sort' => '-created_at,name']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        // 入力トークン→実カラム名のマッピング
        $allowed = [
            'created_at' => 'created_at',
            'name' => 'name',
            'email' => 'email',
        ];
        $instance = new ThenOrderByMulti($allowed, 'sort');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
ORDER BY
  `users`.`created_at` desc,
  `users`.`name` asc
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '複数order by のクエリが一致しません');
    }
}
