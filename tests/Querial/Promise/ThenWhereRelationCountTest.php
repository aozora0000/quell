<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereRelationCount;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereRelationCountTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 件数以上で絞り込む(): void
    {
        // count=2 で items リレーションの件数が2件以上のユーザーに絞り込み
        $request = Request::create('/', 'GET', ['count' => '2']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereRelationCount('items');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  (
    SELECT
      count(*)
    FROM
      `items`
    WHERE
      `users`.`id` = `items`.`user_id`
  ) >= 2
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '件数以上のwhereHasサブクエリが一致しません');
    }

    #[Test]
    public function 演算子を変更して件数で絞り込む(): void
    {
        // operatorを"="にして count=0 のユーザー（= リレーション未所持）に絞り込み
        $request = Request::create('/', 'GET', ['count' => '0']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereRelationCount('items', '=');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  (
    SELECT
      count(*)
    FROM
      `items`
    WHERE
      `users`.`id` = `items`.`user_id`
  ) = 0
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '演算子指定時のwhereHasサブクエリが一致しません');
    }

    #[Test]
    public function 関連側に条件を付与して件数で絞り込む(): void
    {
        // リクエストに name と count を与え、関連側に追加条件を付けた上で件数で絞り込み
        $request = Request::create('/', 'GET', ['name' => 'test', 'count' => '1']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $aggregator = new ThenPromisesAggregator([
            // サブクエリ内で users.name = 'test' を条件に追加
            new ThenWhereEqual('name', null, 'users'),
        ]);
        $instance = new ThenWhereRelationCount('items', '>=', 'count', $aggregator);

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
      AND `users`.`name` = 'test'
  )
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '関連側条件付きのwhereHas件数サブクエリが一致しません');
    }
}
