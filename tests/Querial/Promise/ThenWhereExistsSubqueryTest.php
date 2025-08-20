<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereExistsSubquery;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereExistsSubqueryTest extends WithEloquentModelTestCase
{
    #[Test]
    public function exists指定で_where_existsサブクエリが適用される(): void
    {
        // exists=1 で items テーブルに関連が存在するユーザーに絞り込み
        $request = Request::create('/', 'GET', ['exists' => '1', 'status' => 'active']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        // 子サブクエリに条件（例: items.status = 'active'）を追加
        $aggregator = new ThenPromisesAggregator([
            new ThenWhereEqual('status', null, 'items'),
        ]);
        $instance = new ThenWhereExistsSubquery('items', 'user_id', 'id', 'exists', $aggregator);

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  EXISTS (
    SELECT
      *
    FROM
      `items`
    WHERE
      `users`.`id` = `items`.`user_id`
      AND `items`.`status` = 'active'
  )
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'existsサブクエリのクエリが一致しません');
    }
}
