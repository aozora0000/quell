<?php

namespace Tests\Querial\Promise;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Querial\Promise\ThenWhereEqual;
use Tests\Querial\WithEloquentModelTestCase;

/**
 * @property Builder $builder
 */
class ThenWhereEqualTest extends WithEloquentModelTestCase
{
    public function testResolve(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // リクエストに存在するキーでwhereを掛ける
        $query = (new ThenWhereEqual('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" = 'test'
EOT;
        $this->assertSame($sql, $this->format($query));

        // リクエストに存在するキーでand whereを掛ける
        $query = (new ThenWhereEqual('email'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" = 'test'
  AND "users"."email" = 'email@email.com'
EOT;
        $this->assertSame($sql, $this->format($query));

        // リクエストに存在しないキーの場合、SQLには反映されない
        $query = (new ThenWhereEqual('noattr'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" = 'test'
  AND "users"."email" = 'email@email.com'
EOT;
        $this->assertSame($sql, $this->format($query));
    }

    public function testResolveAnotherTable(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // 検索するテーブルを指定してクエリを作成する
        $query = (new ThenWhereEqual('name', null, 'items'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "items"."name" = 'test'
EOT;
        $this->assertSame($sql, $this->format($query));
    }
}
