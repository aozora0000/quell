<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereNotInSplitArray;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotInSplitArrayTest extends WithEloquentModelTestCase
{
    #[Test]
    public function カンマ区切りの文字列を_where_not_inに変換して適用できる(): void
    {
        // ids="a,b,c" を NOT IN ('a','b','c') に変換できることを確認する
        $request = Request::create('/', 'GET', ['ids' => 'a,b,c']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        // 対象カラムは id、入力キーは ids
        $instance = new ThenWhereNotInSplitArray('id', 'ids');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`id` NOT IN ('a', 'b', 'c')
EOT;
        $actual = $this->format($instance->resolve($request, $builder));
        $this->assertSame(mb_strtolower($expected), $actual, 'カンマ区切りの where not in が期待通りではありません');
    }
}
