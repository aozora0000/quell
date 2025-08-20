<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereInSplitArray;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereInSplitArrayTest extends WithEloquentModelTestCase
{
    #[Test]
    public function カンマ区切りの文字列を_where_inに変換して適用できる(): void
    {
        // ids="a,b,c" を IN ('a','b','c') に変換できることを確認する
        $request = Request::create('/', 'GET', ['ids' => 'a,b,c']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        // 対象カラムは id、入力キーは ids
        $instance = new ThenWhereInSplitArray('id', 'ids');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`id` IN ('a', 'b', 'c')
EOT;
        $actual = $this->format($instance->resolve($request, $builder));
        $this->assertSame(mb_strtolower($expected), $actual, 'カンマ区切りの where in が期待通りではありません');
    }

    #[Test]
    public function 空白や空要素を含む入力を正規化して_where_inを適用できる(): void
    {
        // 余分な空白や空要素は取り除かれ、'a','b' のみが where in に使われること
        $request = Request::create('/', 'GET', ['ids' => ' , a , , b ']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereInSplitArray('id', 'ids');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`id` IN ('a', 'b')
EOT;
        $actual = $this->format($instance->resolve($request, $builder));
        $this->assertSame(mb_strtolower($expected), $actual, '空白や空要素の正規化が正しくありません');
    }
}
