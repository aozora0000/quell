<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereInNumericRangeList;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereInNumericRangeListTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 数値レンジリストから_or条件のbetweenと等価比較が構築される(): void
    {
        // ranges="1-3,5,8-10" → (between 1 and 3) or (=5) or (between 8 and 10)
        $request = Request::create('/', 'GET', ['ranges' => '1-3,5,8-10']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereInNumericRangeList('score', 'ranges');

        $expected = <<<'EOT'
select
  *
from
  `users`
where
  (
    `users`.`score` between '1'
    and '3'
    or `users`.`score` = '5'
    or `users`.`score` between '8'
    and '10'
  )
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '数値レンジリストのOR条件が期待通りではありません');
    }
}
