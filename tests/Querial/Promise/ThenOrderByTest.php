<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenOrderBy;
use Tests\Querial\WithEloquentModelTestCase;

class ThenOrderByTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 指定カラムと方向で_order_byが適用される(): void
    {
        // column=name, dir=desc の指定で ORDER BY `users`.`name` desc が適用されること
        $request = Request::create('/', 'GET', ['column' => 'name', 'dir' => 'desc']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        // 許可カラム: name, email。デフォルトは id asc
        $instance = new ThenOrderBy(['name', 'email'], 'column', 'dir', ['id', 'asc']);

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
ORDER BY
  `users`.`name` desc
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'order by のクエリが一致しません');
    }
}
