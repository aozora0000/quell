<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereLikeMultiColumns;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereLikeMultiColumnsTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 単一キーワードで複数カラムの_or_likeが適用される(): void
    {
        $request = Request::create('/', 'GET', ['q' => 'foo']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereLikeMultiColumns(['name', 'email'], 'q');

        $sql = $this->format($instance->resolve($request, $builder));

        $expected = <<<'EOT'
select
  *
from
  `users`
where
  (
    `users`.`name` like '%foo%'
    or `users`.`email` like '%foo%'
  )
  and (
    `users`.`name` like '%foo%'
    or `users`.`email` like '%foo%'
  )
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'クエリが一致しません');
    }
}
