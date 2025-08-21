<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereNotExistsSubquery;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotExistsSubqueryTest extends WithEloquentModelTestCase
{
    #[Test]
    public function not_exists指定で_where_not_existsサブクエリが適用される(): void
    {
        // not_exists=1 で items テーブルに関連が存在しないユーザーに絞り込み
        $request = Request::create('/', 'GET', ['not_exists' => '1', 'status' => 'inactive']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        // 子サブクエリに条件（例: items.status = 'inactive'）を追加
        $aggregator = new ThenPromisesAggregator([
            new ThenWhereEqual('status', null, 'items'),
        ]);
        $instance = new ThenWhereNotExistsSubquery('items', 'user_id', 'id', 'not_exists', $aggregator);

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  NOT EXISTS (
    SELECT
      *
    FROM
      `items`
    WHERE
      `users`.`id` = `items`.`user_id`
      AND `items`.`status` = 'inactive'
  )
EOT;
        $actual = $this->format($instance->resolve($request, $builder));
        $this->assertSame(mb_strtolower($expected), $actual, 'not existsサブクエリのクエリが一致しません');
    }
}
